#!/usr/bin/env python3

# TODO: fillna with empty string before adding things instead of nulls

from collections import defaultdict
from io import StringIO
from itertools import chain
import re

from sqlalchemy import MetaData, Table, create_engine, select, distinct
import numpy as np
import pandas as pd
import postgres_copy

frontage_re = re.compile(r"[; ][NEWS][EW]? [1-9][0-9]*[\s\.'/][0-9]*\s*[\.'/]?[0-9]*[\s']*")
space_or_dash_re = re.compile(r"[ -]")
is_valid_re = re.compile(r"^ ?CITY .+; ?SBD")

connection_string = 'postgresql://free:freethelots@127.0.0.1:5432/freethelots'
# Saw suggestion for `execution_options={'stream_results': True}`,
# but it doesn't work with DML queries
engine = create_engine(connection_string)
metadata = MetaData(bind=engine)

raw_source = Table('raw_source', metadata, autoload=True)
combined_legal_df = pd.read_sql_query(
    (
        select([
            distinct(raw_source.c.combined_legal)
        ])
        # .where(raw_source.c.combined_legal.op('SIMILAR TO')('CITY [A-Z0-9 ]+; SBD%'))
        .order_by(raw_source.c.combined_legal)
        # .limit(50000)
    ),
    engine
)
print("Got {} unique combined_legals.".format(combined_legal_df.shape[0]))

type_remaps = defaultdict(lambda: None, {
    'BLK': 'BLOCK',
    'SBD': 'SUBDIVISION',
    'LT': 'LOT'
})
known_types = {
    'BLK',
    'SBD',
    'CITY',
    'LT',
    'FRONTAGE',
}
# Key is the component and the value is a set of combined legals that it was found in
known_bad_components = defaultdict(set)


def c(component_type):
    return (type_remaps[component_type] or component_type).lower()


def _parse(combined_legal):
    if not is_valid_re.match(combined_legal):
        return
    seen = set()
    for component in combined_legal.split(';'):
        component = component.strip()
        try:
            ctype, cvalue = component.split(' ', 1)
        except ValueError:
            known_bad_components[component].add(combined_legal)
            continue
        if ctype in seen:
            continue  # Prefer earlier components in case of "duplicates"
        elif ctype == 'LT':
            parts = space_or_dash_re.split(cvalue, 1)
            start = parts[0]
            end = len(parts) == 2 and parts[1] or start
            yield 'LT', "{}-{}".format(start, end)
        elif ctype == 'BLK':
            yield ctype, space_or_dash_re.split(cvalue, 1)[0]
        elif ctype in known_types:
            yield ctype, cvalue
        seen.add(ctype)
    frontage = frontage_re.search(combined_legal)
    if frontage:
        yield 'FRONTAGE', frontage.group()


def parse(combined_legal):
    record = {
        c(key): value.strip()
        for key, value in _parse(combined_legal)
    }
    record[c('COMBINED')] = combined_legal
    return record


def to_table(df, table_name):
    print("Writing {}".format(table_name))
    csv = StringIO()
    df.to_csv(csv, index_label='id')
    csv.seek(0)

    table = Table(table_name, metadata)
    conn = engine.connect()
    with conn.begin():
        conn.execute('TRUNCATE {} RESTART IDENTITY CASCADE;'.format(table_name))
        postgres_copy.copy_from(
            csv,
            table,
            conn,
            columns=chain(['id'], df.columns),
            format='csv',
            freeze=True,
            header=True,
        )


parsed_combined_legal_df = pd.DataFrame.from_records(
    combined_legal_df['combined_legal'].map(parse),
    columns=chain([c('COMBINED')], (c(key) for key in known_types)),
)
print("Got {} distinct bad components.".format(len(known_bad_components.keys())))

unique_parsed = (
    parsed_combined_legal_df
    .fillna(-1)
    .groupby(['city', 'subdivision', 'block', 'lot', 'frontage'])
    .aggregate(set)
    .reset_index()
    .replace(-1, np.NaN)
)
# Bump the index to start at 1 for row IDs
unique_parsed.index += 1
unique_parsed['parsable'] = unique_parsed.apply(lambda x: x.drop('combined').notnull().any(), axis=1)
print("Parsed {} unique legal descriptions.".format(unique_parsed.shape[0]))

parcel_df = (
    unique_parsed
    .drop(['combined', 'parsable'], axis=1)
    .dropna(how='all')
)

parsed_to_combined = (
    {'parcel_id': parsable and idx or np.NaN, 'description': legal}
    for idx, parsable, legals in unique_parsed[['parsable', 'combined']].itertuples()
    for legal in legals
)
# Use `object` to avoid parcel_id being homogenized to Float for NaNs
parcel_combined_df = pd.DataFrame(parsed_to_combined, dtype=object)
# Bump the index to start at 1 for row IDs
parcel_combined_df.index += 1

# Finally, write it all out
to_table(parcel_df, 'parcel')
to_table(parcel_combined_df, 'parcel_combined')

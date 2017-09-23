# Backend

The backend will be a node application.

# Development

This project uses docker for development (and hopefully future deployment!). Install for [Mac](https://store.docker.com/editions/community/docker-ce-desktop-mac) or [Windows](https://store.docker.com/editions/community/docker-ce-desktop-windows). I'm going to assume linux users can figure it out for their system.

Once installed, run `docker-compose up -d` to start just the database for now. To load the existing test data, run the
following command:

```shell
docker-compose exec postgres psql -U postgres -d free_the_lots -c "\copy subdivisions from /usr/src/app/fixtures/subdivisions.psv with csv delimiter '|'"
```

A node loading alternative will be provided soon.

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Free The Lots</title>
    <meta name="description" content="Identifies which properties have extra det attached to the sale, and list the associated claimants and amounts owed, assisting the city, country, developers, and buyers.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootswatch Styling for Improving the Aesthetics -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/flatly/bootstrap.min.css">
    <!-- Font Awesome CSS Icons (For cool glyphicons) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Local Style Sheet -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<!-- Main Bootstrap Search -->
<div class="container">

    <!-- Jumbotron for Title -->
    <div class="jumbotron" style="background-color: #20315A; color: white;">
        <h1 class="text-center"><span class="text-info glyphicon glyphicon-home"></span><span class="text-info strong"> Free The Lots!</span>
            <span class="text-info glyphicon glyphicon-home"></span>
        </h1>
    </div>
    <!-- /.jumbotron -->


    <div class="row">
        <div class="col-sm-12">
            <br>
            <!-- This panel will initially be made up of a panel and wells for each of the retrieved Properties-->
            <div class="panel panel-warning">
                <!-- Panel Heading for the retrieved property data box -->
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Search Options</strong></h3>
                </div>
                <div class="row search-row">
                    <div class="col-lg-12">
                        <div class="input-group">
                                <span class="input-group-btn">
                      <button class="btn btn-info" type="button"  onclick="searchParcels($('#business_name').val())"><span class="glyphicon glyphicon-search"></span>                                Search!</button>
                                </span>
                            <input  id="business_name" type="text" class="form-control" placeholder="Search for properties by: Lender name / Busisness Name">
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row search-row">
                    <div class="col-lg-12">
                        <div class="input-group">
                                <span class="input-group-btn">
                          <button class="btn btn-info" type="button" onclick="searchParcels($('#individual_name').val())"><span class="glyphicon glyphicon-search" ></span>                                Search!</button>
                                </span>
                            <input id="individual_name" type="text" class="form-control" placeholder="Search for properties by: Individual Name [First, Last]">
                        </div>
                        <!-- /input-group -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-info btn-search" onclick="exportCsv(deeds)">Claimants by Number of Claims</button>
                    <!-- <button type="button" class="btn btn-info btn-search">Busisness Name</button> -->
                    <!-- <button type="button" class="btn btn-info btn-search">Right</button> -->
                </div>
            </div>
            <!-- /panel -->
        </div>
        <!-- /col -->
    </div>
    <!-- /row -->

    <!-- This row will handle all of the retrieved property data -->
    <div class="row">
        <div class="col-sm-12">
            <br>
            <!-- This panel will initially be made up of a panel and wells for each of the retrieved Properties-->
            <div class="panel panel-success">
                <!-- Panel Heading for the retrieved property data box -->
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Property Info!</strong></h3>
                </div>
                <!-- This main panel will hold each of the resulting property's data -->
                <div class="panel-body" id="well-section">
                    <div class="row header-row">
                        <div class="col-xs-3 property-header">Claimant</div>
                        <div class="col-xs-2 text-center property-header">Date of Claim</div>
                        <div class="col-xs-3 text-center property-header">Document Type</div>
                        <div class="col-xs-4 text-center property-header">Legal Description</div>
                    </div>
                </div>
                <div class="panel-body" id="property-container">
                    <!-- DOM -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Region -->
    <div class="row">
        <div class="col-sm-12">
            <!-- Line Break followed by closing -->
            <hr>
            <h5 class="text-center"><small>Made by Doodle with lots and lots of <i class="fa fa-heart"></i></small></h5>
        </div>
    </div>
</div>

<!-- jQuery JS -->
<script src="https://code.jquery.com/jquery.js"></script>
<!-- Code to a JavaScript File -->
<!-- script src="app.js"></script -->
<script>

    /**
     * ROUTES
     * /parcels #all parcels
     * /parcels/search/'name' #searches for parcels matching name
     * /parties #all parties
     * /parties/search/
     */
    /* Example Request */
    var deeds = '';

    function searchParcels(search) {
        //var search = $('#individual_name').val();
        $.get('/parcels/search/'+search, function (data) {
            console.log(data);
            var property_container = $('#property-container');
            property_container.empty();
            data.forEach(function (row) {
                console.log(row);
                var legal = row['COMBINED_LEGAL'] ;
                property_container.append('<div id="item-'+ row['id']+'" class="row"></div>');

                var item = $('#item-'+ row['id']);
                item.append('<div class="col-xs-3 ">'+ row['Grantee'] +'</div>');
                item.append('<div class="col-xs-2 text-center ">'+ row['DATE_RECEIVED'] +'</div>');
                item.append('<div class="col-xs-3 text-center ">'+ row['DOCUMENT_TYPE'] +'</div>');
                item.append('<div class="col-xs-4 ">'+ legal +'</div>');
                property_container.append('<div class="col-xs-12"><hr></div>');

            })
            deeds = data;

        })
    }
    function exportCsv (JSONData) {


        var ReportTitle = 'csv';
        var ShowLabel = 'csv';

        //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

        var CSV = '';
        //Set Report title in first row or line

        CSV += ReportTitle + '\r\n\n';

        //This condition will generate the Label/Header
        if (ShowLabel) {
            var row = "";

            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {

                //Now convert each value to string and comma-seprated
                row += index + ',';
            }

            row = row.slice(0, -1);

            //append Label row with line break
            CSV += row + '\r\n';
        }

        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            var row = "";

            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                row += '"' + arrData[i][index] + '",';
            }

            row.slice(0, row.length - 1);

            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {
            alert("Invalid data");
            return;
        }

        //Generate a file name
        var fileName = "MyReport_";
        //this will remove the blank-spaces from the title and replace it with an underscore
        fileName += ReportTitle.replace(/ /g,"_");

        //Initialize file format you want csv or xls
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

        // Now the little tricky part.
        // you can use either>> window.open(uri);
        // but this will not work in some browsers
        // or you will not get the correct file extension

        //this trick will generate a temp <a /> tag
        var link = document.createElement("a");
        link.href = uri;

        //set the visibility hidden so it will not effect on your web-layout
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";

        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

    }

</script>

</body>

</html>
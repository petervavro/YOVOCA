<html>
    <head>
        <title>YOVOCA</title>
        <style type="text/css">
            body
            {
                font-family: "Trebuchet MS",Verdana,Helvetica,Arial,sans-serif;
                text-align:center;
                background-color: #ffffff;
            }

            div#uwlist table{
                width:100%;
            }

            div#uwlist table th{
                border: #3d3d3d solid 1px;
                color:#3d3d3d;
                text-align:center;
                font-size:small;
            }

            div#uwlist table td{
                /* background:#f9f9f9;*/
                border:#3d3d3d 1px solid;
                font-size:larger;
            }
        </style>
    </head>
    <body>
        <?php
            if (isset($moviename)) {
                echo heading($moviename, 3);
            }

            if (isset($content)) {
                echo $content;
            }
        ?>
    </body>
</html>


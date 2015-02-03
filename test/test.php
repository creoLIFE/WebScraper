<?php

require_once('../vendor/autoload.php');
require_once('../Library/Creolife/Webscraper.php');

$scraper = new \creoLIFE\Webscraper();



if( isset($_POST['url']) and isset($_POST['xpath']) ){

    $value = $scraper->parse($_POST['url'],
        array(
            array(
                'xpath'     => $_POST['xpath'],
                'valueType' => 'html',
                'block'     => preg_replace('/\[[^\[]*\](?!\[)$/', '', $_POST['elonlist']),
                'blockText' => $_POST['elonlisttext'],
                'regex'     => $_POST['regex']
            )
        )
    );

    echo json_encode( $value );
    die();
}

?>
<html>
<body>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready( function(){

            $('#btnSubmit').on('click', function(){
                var request = $.ajax({
                    url: "test.php",
                    type: "POST",
                    data: {
                        url : $('#iUrl').val(),
                        xpath : $('#iXpath').val(),
                        elonlist : $('#iElonlist').val(),
                        elonlisttext : $('#iElonlisttext').val(),
                        regex : $('#iRegex').val()
                    },
                    dataType: "json"
                });

                request.done(function(json){
                    var popup = window.open('','popup323424242344235623','width=1300,height=900,scrollbars=yes');
                    jQuery(popup.document.body).append(json.values[0].value);
                    console.log(json);
                });
                return false;
            });
        });
    </script>


<style>
#ftest {font:10px/12px tahoma;}
#ftest label {display:block; float:left; width:300px;}
#ftest input {display:block; float:left;}
#ftest dd {overflow:hidden; width:900px;}
</style>
<form id="ftest" method="post">
    <dl>
        <dd>
            <label>URL</label>
        </dd>
        <dd>
            <input id="iUrl" name="url" style="width:500px" value="http://www.creolife.pl"></input>
        </dd>
    </dl>
    <dl>
        <dd>
            <label>xPath</label>
        </dd>
        <dd>
            <input id="iXpath" name="xpath" style="width:300px" value="/html/body/div"></input>
        </dd>
    </dl>
    <dl>
        <dd>
            <label>Element (block) on List</label>
        </dd>
        <dd>
            <input id="iElonlist" name="elonlint" style="width:250px" value=""></input>
        </dd>
    </dl>
    <dl>
        <dd>
            <label>Text on element (block) to identify proper block on list</label>
        </dd>
        <dd>
            <input id="iElonlisttext" name="elonlisttext" style="width:250px" value=""></input>
        </dd>
    </dl>
    <dl>
        <dd>
            <label>Regex</label>
        </dd>
        <dd>
            <input id="iRegex" name="regex" style="width:250px" value=""></input>
        </dd>
    </dl>
    <dl>
        <dd>
            <button id="btnSubmit" type="submit">GET</button>
        </dd>
    </dl>
</form>

</body>
</html>
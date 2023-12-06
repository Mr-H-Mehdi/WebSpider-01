<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMK's Web Spider</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap');

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            /* display: flex; */
            /* align-items: center; */
            justify-content: center;
            /* height: 100vh; */
            
        }
        .container {
            
            top: 0;
            left: 0;
            /* width: 100%; */
            height: 25%;
            min-height: 160px;
            background-color: #fff;
            padding: 10px 30px 0px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* overflow-x: hidden;  */
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
        }
        input[type="text"] {
            max-width: 800px;
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        #btns-continer{
            display: flex;
            /* justify-content: space-between;         */
        }
        select {
            /* width: 90%; */
            flex: 1;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        select:hover{
            background-color: #ccc;
        }
        button {
            /* width: 90%; */
            flex: 3;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
        @media screen and (max-width: 600px) {
            .container {
                width: 100%;
                padding: 20px;
            }
        }

        #result{
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>HMK's Web Spider</h1>
        <form action="" method="post" id="btns-container">
            <input type="text" name="text" id="text" placeholder="Enter URL to Crawl or Text to Search">
            <div id="btns-container">
                <select name="action" id="action">
                    <option value="crawl">Crawl</option>
                    <option value="search">Search</option>
                </select>
                <button type="button" id="Submit">Proceed</button>
                <p id="displaymsg"></p>
            </div>
        </form>
        
    </div>
    <div class="results" id="result">
        <!-- Your result container -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function(){
            // Click event for the button
            $("#Submit").click(function(){
                // Get data from the form
                var text = $("#text").val();
                var action = $("#action").val();
                
                //  validate Url 
                if (text.trim() === '') {
                    alert('URL cannot be empty');
                    return; // Exit the function if the URL is empty
                }

                $("#text").val('');
                $("#result").val('');
                
                
                var phpScript = (action === 'crawl') ? 'scripts/crawl.php' : 'scripts/search.php';
                
                // Rest of your AJAX logic...
                var postData = {
                    text: text,
                    action: action
                    // Add more data as needed
                };

                // AJAX request
                $.ajax({
                    type: "POST",
                    url: phpScript, // Replace with the path to your PHP script
                    data: postData,
                    success: function(response) {
                        // Update the content of the 'result' div with the response from the server
                        $("#result").html(response);
                    }
                });
            });

            
        });
    </script>
</body>
</html>
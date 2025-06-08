<?php
session_start();
// DB credentials 
$db_host = 'localhost'; 
$db_user = 'root';      
$db_pass = '';        
$db_name = 'mental_health'; 

// Check session for form1 answers
if (!isset($_SESSION['form1_answers'])) {
    // If form1 data is missing, display an error message using the styled box
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #e0f2f7;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
            }
            .message-box {
                background-color: #ffffff;
                border: 2px solid #ffcc00; /* Warning border color */
                border-radius: 16px;
                padding: 32px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                max-width: 600px;
                width: 100%;
                box-sizing: border-box;
            }
            h2 {
                color: #e05c00; /* Warning heading color */
                font-size: 1.875rem;
                font-weight: bold;
                margin-bottom: 16px;
            }
            p {
                color: #4a5568;
                font-size: 1.125rem;
                margin-bottom: 12px;
                font-weight: 500;
            }
        </style>
    </head>
    <body>
        <div class="message-box">
            <h2>Error!</h2>
            <p>Form 1 data missing. Please complete the first survey form.</p>
        </div>
    </body>
    </html>
    <?php
    exit(); // Stop execution after displaying the error
}

$form1 = $_SESSION['form1_answers'];

// Check if form2 answers and personal info are set
if (
    isset($_POST['Q6']) && isset($_POST['Q7']) && isset($_POST['Q8']) &&
    isset($_POST['Q9']) && isset($_POST['Q10'])
) {
    // Sanitize inputs
    $Name = $form1['Name'];
    $Email = $form1['Email'];
    $Phone = $form1['Phone'];
    $Age = $form1['Age'];
    $Gender = $form1['Gender'];

    if (!$Email) {
        // If email is invalid, display an error message using the styled box
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background-color: #e0f2f7;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                    text-align: center;
                }
                .message-box {
                    background-color: #ffffff;
                    border: 2px solid #ffcc00; /* Warning border color */
                    border-radius: 16px;
                    padding: 32px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    max-width: 600px;
                    width: 100%;
                    box-sizing: border-box;
                }
                h2 {
                    color: #e05c00; /* Warning heading color */
                    font-size: 1.875rem;
                    font-weight: bold;
                    margin-bottom: 16px;
                }
                p {
                    color: #4a5568;
                    font-size: 1.125rem;
                    margin-bottom: 12px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h2>Error!</h2>
                <p>Invalid email format.</p>
            </div>
        </body>
        </html>
        <?php
        exit(); // Stop execution
    }

    // Convert Q6-Q10 to integers
    $Q6 = intval($_POST['Q6']);
    $Q7 = intval($_POST['Q7']);
    $Q8 = intval($_POST['Q8']);
    $Q9 = intval($_POST['Q9']);
    $Q10 = intval($_POST['Q10']);

    // Calculate total score including form1 and form2
    $overall_total = $form1['total_score'] + $Q6 + $Q7 + $Q8 + $Q9 + $Q10;

    // Connect to DB
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($mysqli->connect_error) {
        // If DB connection fails, display an error message using the styled box
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background-color: #e0f2f7;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                    text-align: center;
                }
                .message-box {
                    background-color: #ffffff;
                    border: 2px solid #ff0000; /* Error border color */
                    border-radius: 16px;
                    padding: 32px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    max-width: 600px;
                    width: 100%;
                    box-sizing: border-box;
                }
                h2 {
                    color: #cc0000; /* Error heading color */
                    font-size: 1.875rem;
                    font-weight: bold;
                    margin-bottom: 16px;
                }
                p {
                    color: #4a5568;
                    font-size: 1.125rem;
                    margin-bottom: 12px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h2>Database Connection Error!</h2>
                <p>Database connection failed: <?php echo $mysqli->connect_error; ?></p>
            </div>
        </body>
        </html>
        <?php
        exit(); // Stop execution
    }

    // Prepare insert statement to prevent SQL injection
    $stmt= $mysqli->prepare("INSERT INTO mental_health_survey 
        (Name, Email, Phone, Age, Gender, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10, TotalScore) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssiiiiiiiiiiiii",
        $Name, $Email, $Phone, $Age, $Gender,
        $form1['Q1'], $form1['Q2'], $form1['Q3'], $form1['Q4'], $form1['Q5'],
        $Q6, $Q7, $Q8, $Q9, $Q10,
        $overall_total
    );

    if ($stmt->execute()) {
        // Clear session data after successful insert
        unset($_SESSION['form1_answers']);
        // Output the success HTML
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Submission Successful!</title>
            <style>
                body {
                    font-family: 'Inter', sans-serif; /* Using Inter font for a clean look */
                    background-color: #e0f2f7; /* A light, mentally soothing blue-green color */
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh; /* Ensure it takes full viewport height */
                    margin: 0;
                    padding: 20px; /* Add some padding around the content */
                    box-sizing: border-box; /* Include padding in element's total width and height */
                    text-align: center; /* Center align all text within the body */
                }

                .message-box {
                    background-color: #ffffff; /* White background for the message box */
                    border: 2px solid #a7d9f2; /* A subtle blue border */
                    border-radius: 16px; /* Nicely rounded corners for the box */
                    padding: 32px; /* Ample padding inside the message box */
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Soft shadow for depth */
                    max-width: 600px; /* Limit the width of the box for readability */
                    width: 100%; /* Make it responsive */
                    box-sizing: border-box; /* Include padding and border in the element's total width and height */
                }

                h2 {
                    color: #2c5282; /* Darker blue for headings */
                    font-size: 1.875rem; /* Large font size for the heading */
                    font-weight: bold;
                    margin-bottom: 16px; /* Space below the heading */
                }

                p {
                    color: #4a5568; /* Slightly darker text for messages */
                    font-size: 1.125rem; /* Larger font size for messages */
                    margin-bottom: 12px; /* Space below paragraphs */
                    font-weight: 500; /* Medium font weight */
                }

                .complementary-message {
                    margin-top: 24px; /* Space above the complementary message */
                    font-size: 0.9rem; /* Smaller font for this message */
                    color: #6b7280; /* A soft grey color */
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h2>Thank you, your responses have been recorded successfully.</h2>
                <p><strong>Your Total Mental Health Score:</strong> <?php echo $overall_total; ?></p>
                <p class="complementary-message">Designed and developed by Aishwarya with ❤</p>
            </div>
        </body>
        </html>
        <?php
    } else {
        // If saving data fails, display an error message using the styled box
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background-color: #e0f2f7;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                    text-align: center;
                }
                .message-box {
                    background-color: #ffffff;
                    border: 2px solid #ff0000; /* Error border color */
                    border-radius: 16px;
                    padding: 32px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                    max-width: 600px;
                    width: 100%;
                    box-sizing: border-box;
                }
                h2 {
                    color: #cc0000; /* Error heading color */
                    font-size: 1.875rem;
                    font-weight: bold;
                    margin-bottom: 16px;
                }
                p {
                    color: #4a5568;
                    font-size: 1.125rem;
                    margin-bottom: 12px;
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h2>Error Saving Data!</h2>
                <p>Error saving data: <?php echo $stmt->error; ?></p>
            </div>
        </body>
        </html>
        <?php
    }

    $stmt->close();
    $mysqli->close();
} else {
    // If form fields are incomplete, display an error message using the styled box
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #e0f2f7;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
            }
            .message-box {
                background-color: #ffffff;
                border: 2px solid #ffcc00; /* Warning border color */
                border-radius: 16px;
                padding: 32px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                max-width: 600px;
                width: 100%;
                box-sizing: border-box;
            }
            h2 {
                color: #e05c00; /* Warning heading color */
                font-size: 1.875rem;
                font-weight: bold;
                margin-bottom: 16px;
            }
            p {
                color: #4a5568;
                font-size: 1.125rem;
                margin-bottom: 12px;
                font-weight: 500;
            }
        </style>
    </head>
    <body>
        <div class="message-box">
            <h2>Error!</h2>
            <p>Please complete all fields in the survey.</p>
        </div>
    </body>
    </html>
    <?php
}
?>
<?php

header("Content-Type: application/json");

$response = [];

if(isset($_POST['formData']))
{

    $formData = json_decode(
        $_POST['formData'],
        true
    );

    if(!file_exists("../uploads"))
    {
        mkdir("../uploads");
    }

    foreach($_FILES as $key => $file)
    {

        $fileName =
            time() . "_" . $file['name'];

        move_uploaded_file(
            $file['tmp_name'],
            "../uploads/" . $fileName
        );

    }

    $save = [

        "data" => $formData,

        "created_at" => date("Y-m-d H:i:s")

    ];

    file_put_contents(

        "../data/submitted-data.json",

        json_encode(
            $save,
            JSON_PRETTY_PRINT
        )

    );

    $response = [

        "status" => true,

        "message" => "Form Submitted Successfully"

    ];

}
else
{

    $response = [

        "status" => false,

        "message" => "No Data Received"

    ];

}

echo json_encode($response);
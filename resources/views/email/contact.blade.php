<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
</head>

<body>
    <h1>You have received a contact email</h1>
    <p>Name: {{$data['name']}}</p>
    <p>Email: {{$data['email']}}</p>
    <p>Subject: {{$data['subject']}}</p>
    <p>Message: {{$data['message']}}</p>
</body>

</html>
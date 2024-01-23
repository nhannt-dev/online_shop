<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>You have requested to change password:</h1>
    <p>Please click the link given below to reset password</p>
    <a href="{{route('front.resetPassword', $data['token'])}}">Click here</a>
</body>

</html>
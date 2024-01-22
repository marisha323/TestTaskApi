<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <form action="/user/process" method="post" class="col-md-6 mx-auto" onsubmit="return validateForm()">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Имя:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Пол:</label>
            <select name="gender" class="form-select" required>
                <option value="male">Мужской</option>
                <option value="female">Женский</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="age" class="form-label">Возраст:</label>
            <input type="number" name="age" class="form-control" required>
        </div>
        <label for="basic-url">Your vanity URL</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
            </div>
            <input type="text" class="form-control" name="audio_url" id="basic-url" aria-describedby="basic-addon3" >
            <input type="file" id="musicfile" name="musicfile" accept="audio/mp3" >
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
</div>
</body>
</html>
<script>
    function validateForm() {
        var nameInput = document.getElementsByName('name')[0];
        var lettersAndSpacesRegex = /^[A-Za-z\s'-]+$/;

        if (nameInput.value.trim() === '') {
            alert('Введіть ім\'я.');
            return false;
        }

        var genderSelect = document.getElementsByName('gender')[0];
        if (genderSelect.value === '') {
            alert('Виберіть стать.');
            return false;
        }

        var ageInput = document.getElementsByName('age')[0];
        if (isNaN(ageInput.value) || ageInput.value < 0) {
            alert('Введіть коректний вік.');
            return false;
        }
        return true;
    }
</script>

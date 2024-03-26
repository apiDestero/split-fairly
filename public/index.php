<?php

include '../src/Database.php';
include '../src/User.php';
include '../src/UserRepository.php';

define('BILL_AMOUNT', 100);
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'test');

try
{
    $db = new MySQLDatabase(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    $queryBuilder = new MySQLQueryBuilder();
    $userRepository = new UserRepository($db, $queryBuilder);

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_REQUEST['reset']))
        {
            $userRepository->deleteAll();
        }
        else
        {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING) ?? null;
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING) ?? null;

            if (!$name || !$email)
            {
                $errors[] = 'Fill all the fields';
            }
            elseif ($email)
            {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $errors[] = 'Invalid email!';
                }
            }

            if (empty($errors))
            {
                $user = new User($name, $email);
                if (!$userRepository->save($user))
                {
                    $errors[] = 'Couldn\'t create new user';
                }
            }
        }
    }

    $users = $userRepository->getAll();

    if (!empty($users))
        $pay = round(BILL_AMOUNT / count($users), 2);
    else $pay = BILL_AMOUNT;

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // while(ob_end_clean());

        // if (ob_get_level()) {
        //     ob_end_clean();
        // }

        $result = [
            'errors' => $errors,
            'users' => $users,
            'pay' => $pay
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }
}
    catch (Exception $e)
{
    $fatal = $e->getMessage();

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // while(ob_end_clean());

        // if (ob_get_level()) {
        //     ob_end_clean();
        // }

        $result = [
            'errors' => [$fatal],
            'users' => [],
            'pay' => BILL_AMOUNT
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Split fairly</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script type="text/javascript" src="script.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php if ($fatal): ?>
        <div class="container">
            <h1 class="fatal-error">Error: <hr> <?php echo $fatal ?></h1>
        </div>
    <?php else: ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var app = new Application({
                errors: <?php echo json_encode($errors)?>,
                users: <?php echo json_encode($users)?>,
                pay: <?php echo $pay?>,
            });
        }, false);
    </script>
    <div class="container">
        <h1 class="headerline">Split the bill with your friends</h1>
        <hr>
        <form id="userForm" method="post" action="/">
            <div class="input-block">
                <div class="item-row">
                    <input type="text" name="name" placeholder="Name">
                </div>
                <div class="item-row">
                    <input type="email" name="email" placeholder="Email">
                </div>
            </div>

            <div class="item-row">
                <button type="submit" class="success">Add user</button>
            </div>

            <div class="item-row">
                <div id="messages"></div>
            </div>
        
            <div class="item-row">
                <table id="userTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>To pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            
                            <tr>
                                <td><?php echo $user['name']?></td>
                                <td><?php echo $user['email']?></td>
                                <td><?php echo $pay?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="item-row">
                <button type="submit" name="reset">Reset</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</body>
</html>
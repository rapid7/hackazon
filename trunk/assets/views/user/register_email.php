<?php
/**
 * @var array $data User data
 */
?>
Welcome to hackazon.com, <?php echo $data['username']; ?>!

Now you can log in with your credentials:

Username: <?php echo $data['username'] . "\n"; ?>
Password: <?php echo $data['password'] . "\n"; ?>

Here is your information:

First name: <?php echo $data['first_name'] . "\n"; ?>
Last name: <?php echo $data['last_name'] . "\n"; ?>
Email: <?php echo $data['email'] . "\n"; ?>

Best regards.
Team of hackazon.com.
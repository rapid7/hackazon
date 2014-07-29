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

Email: <?php echo $data['email'] . "\n"; ?>
Phone: <?php echo $data['user_phone'] . "\n"; ?>

Best regards.
Team of hackazon.com.
CREATE  TABLE `tbl_faq` (
  `faqID` INT NOT NULL AUTO_INCREMENT ,
  `question` VARCHAR(255) NOT NULL ,
  `answer` TEXT NULL ,
  `email` VARCHAR(255) NULL ,
  PRIMARY KEY (`faqID`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `tbl_faq` (`question`, `answer`) 
VALUES ('I want to return my purchase! What do I do?', 
'If you are not 100% satisfied with your purchase from our site you can return your item(s) for a full refund within 365 days of purchase. (Returns must be unworn, in the state you received them, and in the original packaging.)

Did you know that returning merchandise is easy and informative, not to mention, can be fun? Check out this returns video for a sneak peek into some of our custom conference rooms.

Make sure the merchandise is in its original packaging, (e.g., shoebox), and place it in a shipping box.  Attach the label to the sealed box.  Be sure to cover any old labels with the new one, or just peel them off.  A black marker can also be used to cover any existing bar codes.

You may drop off your return at any authorized UPS shipping location, but please do not drop your return off at a drop box.  To find the nearest authorized shipping location, please visit www.ups.com.

It may take 4-5 business days for your return to reach the Hackazon Centers. Once it is received and inspected (usually within 72 hours of receipt) your refund will be processed and automatically applied to your credit card or original method of payment within 7 days. Please note that depending on your credit card company, it may take an additional 2-10 business days after your credit is applied for it to post to your account.'
);


INSERT INTO `tbl_faq` (`question`, `answer`) 
VALUES ('Do you accept international credit cards?', 
'Currently Hackazon can accept international credit cards but we can only ship to an address within the United States and its territories. Please note that the credit card must be issued and contain a logo from either Visa, Mastercard, Discover, or American Express.'
);


INSERT INTO `tbl_faq` (`question`, `answer`) 
VALUES ('What can cause my order to be delayed?', 
'1. If the billing information you provided does not match what your bank has on file (including address and telephone number), your order may be delayed.

2. We all love sending gifts to others and ourselves as much as we love receiving them. However, if you are shipping to an address other than your billing address, your order may be delayed.'
);

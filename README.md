XKCD CHALLENGE

This project is a implementation of rtCamp XKCD Challenge
Check out the live project at
Website : http://xkcdcomic.ictmu.in/


Features :

    -> Users will subscribe them from home page by entring their email address.
    -> After that users will get one verification email in their inbox from that email users have to verify thier email address.
    -> After successful email verification they will get comic mail at every 5 minutes everyday.
    -> In comic email they can unsubscribe them selfs from unsubscribe link.
    -> After successful unsubscription user will get comic email. 


Workflow :

    I) Verification and Validation :

        1) Entry point index.php.
        2) posted form, at first visit Subscriber.php for verification mail send functionality.

    II) Inserting data into Database :

        1) verification mail redirects to index.php where it checks database existence and registers email in database.

    III) Scheduled Mail Service :

        1) all subscribed user will receive XKCD Comic mail every 5 min Sendcomic.php file will be run.

    IV) Unsubscribe :

        1) Unsubscribe link in daily comic mail refers to Unsubscribe.php.


Notes :

    It is possible to get verification mail late due to server issues or comic mails can be come late due to issues of cron job or server.

Created By : 
    
    Name          : Jay Solanki 
    Email         : solanki13jay@gmail.com
    Contact No.   : 9723383053
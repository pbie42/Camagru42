<?php
//TODO Need to make the feed system work. The focus right now should be

//  1. Add the ability to upload a photo in the feed section (use the same process as uploading to a gallery) make the gallery name the username

//  2. When uploading the photo make sure that someone can add a comment to the photo. If no comment is applied have an automatic comment of the date it is uploaded.

//  3. Will apply the comment system to the part above. Each initial comment, whether it is automatic or supplied, will be the "status"
//     and everything commented after by someone is simply a reply to the initial status. Need to use this system so that I can take
//     the photo's unique ID and use the same ID for the status's unique ID. That way only the replies to that certain status will be shown.

//  4. Will also need to figure out how to display that certain grouping of status with that picture in some kind of php loop.

//  5. Need to create a php loop for the index page that will grab all the photos in
//     the photos section of the database. I need to have the loop cycle through each
//     row in the order of the uploaddate and use that information to display each
//     photo. It must take the file name and search for it in the /all folder I have made
//     under the /user folder that holds all of the user photos

//  6. Should probably leave all the galleries as they are and make a brand new thing
//     for the feed. Make sure to go back into my photos files to clean up the changes I've done

//  7. Need to make a fresh feed page. The access to this page will have be done using a check
//     system with the home button. If a user is not logged in the home button will header them
//     them to the login screen, otherwise if they are logged in it header them to the feed.

//TODO Comment deletion system is not working for some reason.
?>
onmouseover="camagru(\''.$i.'\')"

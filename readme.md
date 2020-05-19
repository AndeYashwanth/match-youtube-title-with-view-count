clone the repo and run ```composer install``` to install googleapi and dotenv(optional).<br><br>

create a project in google developer console and add youtube data api v3.<br>

You also need to enable OAuth in dev console.<br><br>

Edit video_id(line 11)(obtained from url of video), api_key(line 12)(obtained from google developer console) and application name(line 15) in main.php.<br>

Download client_secret.json form google developer console and place in same(current) directory.<br>

Open get_auth_code.php file in browser after starting apache2 or xampp.<br>

open the url in new tab and authorise your account to your app.<br>

copy the authorization code from the url to main.php(line 13) and open in browser or run in commandline.<br>

Access token and refresh token along with some metadata like created time and expiry will be stored in accesstoken.json.<br>

views, likes and comments count will be stored in count.json.<br>

If the counts obtained by fetching using yotube data api are different from previously saved count.json then script updates the title.<br>

<b>Run cron job every minute or 30 seconds or how frequently you want depending on your size of audience and quota limit for requests.</b><br>
```crontab -e```
<br>
add ```* * * * * /usr/bin/php path/to/main.php``` at the end of file.
<br><br>
<b>Code obtained from youtube data api v3 docs.</b><br>

<p>Below video demonstrates working of this script.</p>
https://www.youtube.com/watch?v=ScIe7InaRJI

# banglalink data balance scraper
If you are lazy af (like me :expressionless: ) and tired of checking your data balance manually by dialing `*5000*500#`. Then let's automate it :3 

## Setting Up
1. Clone
`git clone https://github.com/NasirNobin/banglalink-data-balance-scraper.git`

2. Run `composer install`

3. [Sign Up](https://www.onlineservice.banglalink.net/UserManagement/SignUpVerifyCode.aspx?Source=BLWebSiteDirectLogInBOS) for onlineservice.banglalink.ne if don't have an account yet.

4. Edit `mybl.php` and update your mobile number & password

```
$this->login = [
    'mobile'   => '019XXXXXXX',
    'password' => 'YOUR_PASS',
];
```
5. Run `php mybl.php`

![Result](https://image.prntscr.com/image/TCgQULbJSHW1ZqDSnhSjFQ.png "Result")

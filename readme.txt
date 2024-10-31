=== Protection Grid ===
Contributors: gcsjames7
Tags: Security, SSO, Protection, Grid, Protection Grid, WAF, Cybersecurity, IP Block, Location Protection, Traffic Monitoring
Requires at least: 4.6
Tested up to: 6.6.1
Stable tag: 1.5.9
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Protection Grid is a comprehensive WordPress security plugin designed to safeguard your website with an array of robust security features. Whether you're running a small blog or a large business site, Protection Grid ensures your site remains secure from threats.

**Key Features:**

* **Single Sign-On (SSO):** Seamlessly integrate SSO for MiBizHosting environments, providing a streamlined and secure login experience for users.
* **Brute Force Protection:** Automatically detects and blocks brute force login attempts, ensuring your login page remains secure from automated attacks.
* **IP Blocking/Filtering:** Easily block or filter IP addresses based on country codes or specific IP ranges, providing an additional layer of security against malicious traffic.
* **Web Application Firewall (WAF):** Protect your website from common web threats and vulnerabilities with our integrated WAF, ensuring your site remains secure from attacks.
* **Traffic Analysis:** Monitor and analyze your website traffic in real-time, allowing you to detect and respond to suspicious activities promptly.

== Changelog ==

= 1.5.7 =
* Disabled  WAF by default

= 1.5.5 =
* Updated Readme
* Corrected header scan error
* Corrected getallheaders error

= 1.5.3 =
* Added Web Application Firewall (WAF)

= 1.5.1 =
* Added uptime to dashboard

= 1.5.0 =
* Login History available

= 1.4.7 =
* Check and clear duplicate Cron Jobs

= 1.4.3 =
* Enabled Backup Process

= 1.4.0 =
* Country Code Filtering & backup added

= 1.3.0 =
* SSO enabled

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/protection-grid` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Protection Grid settings page to configure the plugin according to your needs.

== Frequently Asked Questions ==

= How does Protection Grid protect against brute force attacks? =
Protection Grid employs a combination of login attempt monitoring and IP blocking to prevent brute force attacks. It automatically blocks IP addresses after a specified number of failed login attempts.

= Can I block traffic from specific countries? =
Yes, with the IP Blocking/Filtering feature, you can easily block or filter traffic from specific countries by their country codes.

= What is the Web Application Firewall (WAF)? =
The WAF is a security feature that protects your website from common web threats, such as SQL injection, cross-site scripting (XSS), and other vulnerabilities.

= How do I enable SSO for MiBizHosting environments? =
SSO can be enabled from the Protection Grid settings page. Detailed instructions are provided within the plugin's documentation.

== Screenshots ==

1. **Dashboard Overview:** Get a quick overview of your website's security status.
2. **Login History:** Track login attempts and detect any suspicious activities.
3. **Traffic Analysis:** Monitor your website traffic in real-time.
4. **Settings Page:** Configure the plugin to meet your specific security needs.

== Upgrade Notice ==

= 1.5.4 =
* Make sure to update to this version to benefit from the latest security enhancements and bug fixes.

== Credits ==

Developed by gcsjames7, with contributions from the WordPress community.

== License ==

This plugin is licensed under the GPLv2 or later. For more information, please visit [GNU Licenses](https://www.gnu.org/licenses/gpl-2.0.html).

# McCown Wedding Website
This project is designed to allow invited guests to easily accept the invitation to our wedding and manage their requests. It is designed from the ground up by myself and my fiance. The website is coded entirely JavaScript/JQuery, PHP and MySQL.

## Features
### Fully responsive design
- Responsive design for all screen sizes
- Collapsable "hamburger" menu upon mobile detection
- Responsive design created in all native CSS

### RSVP Functionality
- Automatically assign guests randomly generated alphanumeric IDs 
- Allow guests to reserve the number of additional guests they will be bringing to the wedding. This is a number that can be assigned by the administrator.
- Allow guests to request any songs that they would like to hear. The number they are allowed to reserve can be set by the administrator.
- Allow guests to update their reservations at any time

### Administration Functionality
- Allow bulk importing of guests through CSV files
- Allow viewing of songs requests, guests invited, and guests who have confirmed their attendance in a sortable table
- Allow easy removal of guest invitation
- Allow exporting of songs requests, guests invited, and guests who have confirmed their attendance to a CSV file

## Content
Include information about each member of the bride and groom party. Include map to location of wedding/reception. Include registry information to all registries. Provide background information to about the bride and groom.
### Cookies
Cookies are used to store session information on the McCown Wedding website. Cookies include:
- McCownUser: Used to store the username of an administrator
- McCownPass: Used to store the password of an administrator. Passwords are salted and hashed.
- McCownID: Used to store the unique ID of a guest. This will be used to assist the guest with logging back in to the RSVP portion.

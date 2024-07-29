# Cab Booking
This is an academic project from Swinburne University where I was required to design and develop a cab booking system based on the given requirements.<br>
<h1>CabsOnline User Manual</h1>
This user manual provides a guide to using the CabsOnline web application for both 
regular users and administrators.<br>
<h3>Getting Started</h3>
You need Xammp to run the php file and mysql. When you first try to go to login.php it will 
redirect you to register.php if the required database and tables are not found. Register 
page creates database and tables if it is not available and also adds dummy data if data 
is not available. The default credentials created by this page is given below:<br><br>
Email: admin@cabsonline.com<br>
Password: admin<br><br>
<b>• Regular Users:</b><br>
o Visit the CabsOnline website.<br>
o If you are a new user, you will need to register by clicking the "Register" link 
and providing your details.<br>
o Once registered, you can log in using your email and password.<br><br>
<b>• Administrators:</b><br>
o Only authorized personnel can access the admin dashboard.<br>
o You will need the admin login credentials to access the admin panel.<br>
o Only admin will be able to add new admins.<br><br>
<b>User Roles</b><br>
The CabsOnline application has two user roles:<br>
<b>• Regular User:</b> Can book cabs, view booking details, and update their account 
information.<br>
<b>• Administrator:</b> Can manage all cab bookings, including assigning taxis, viewing 
booking details, and searching for specific bookings.<br><br>
<h3>Regular User Functionalities</h3><br>
<b>• Booking a Cab:</b><br>
o Once logged in, navigate to the "Booking" page.<br>
o Fill out the form with your pickup details (name, phone number, address), 
destination suburb, pickup date and time.<br>
o Ensure all fields are filled and information is accurate.<br>
o Click the "Book Cab" button to submit your request.<br>
o If the booking is successful, you will see a confirmation message with your 
booking reference number, pickup time, and date.<br><br>
<h3>Admin Dashboard Functionalities</h3>
<b>• Manage Booking Requests:</b><br>
o View a list of all unassigned pickup requests scheduled within the next three 
hours.<br>
o View a list of all assigned cab bookings.<br>
o Search for a specific booking using the booking reference number.<br><br>
<b>• Assign Taxis:</b><br>
o Assign a taxi to an unassigned booking request by entering the booking 
reference number.<br>
<h3>Additional Information</h3><br>
• The system validates all user inputs to ensure data accuracy and security.<br>
• For any failed bookings or errors, the system will display clear error messages.<br>
<h3>Core Application Files:</h3><br>
• login.php: This file handles user login functionality. It verifies user credentials 
(email and password) against a database and redirects users to the appropriate 
page (admin dashboard or booking interface) based on their login status.<br>
• register.php: This file manages user registration. It allows users to create new 
accounts by providing their details and stores them in the database. It also 
performs checks to ensure email uniqueness and password strength.<br>
• booking.php: This file handles the cab booking process. Users can fill out a form 
with pickup details, destination, and desired pickup time. The script validates the 
information, interacts with the database to store booking details, and provides a 
confirmation message upon successful booking.<br>
• admin.php: This file represents the admin dashboard. It allows authorized 
administrators to manage cab bookings. They can view lists of unassigned and 
assigned bookings, search for specific bookings using reference numbers, and 
assign taxis to unassigned requests.<br>
<h4>Styling Files:</h4><br>
• style.css: This file contains the styles applied to most pages of the application, 
controlling the visual layout and appearance of elements.<br>
• admin_style.css: This file is a separate stylesheet specifically designed for the 
admin dashboard, providing a unique look and feel compared to the regular user 
interface.

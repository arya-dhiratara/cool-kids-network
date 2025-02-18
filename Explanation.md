
# Explanation.md

## The Cool Kids Network plugin

Cool Kids Network is a WordPress plugin that implements a user management system, serving as a proof of concept (PoC) for a WordPress Developer Technical Assessment.  


## **Features**  

Currently the features are limited to:
- **User Registration**: Users can sign up using their email, and a character is automatically created for them.  
- **Random Character Generation**: Upon registration, the plugin generates a random first name, last name, and country using [randomuser.me](https://randomuser.me) API.  
- **User Login**: Users can log in using only their email (no password required for this PoC).  
- **User Roles**: Three roles exist in the system:  
  - `cool_kid` (default): Can view only their own character data.  
  - `cooler_kid`: Can view other user's name and country, but not email and role.  
  - `coolest_kid`: Can view other user's name, country, email, and role.  
- **Role Management via API**: The website admin can change user roles using a secure REST API. 

This proof of concept (PoC) demonstrates the ability to create users using data that is generated from randomuser.me API, assign and manage roles via API endpoints.

## Technical Specification

The project is developed as a WordPress plugin and follows modern object-oriented programming (OOP) principles. The key components include:

- **User Registration**: A new user is created with an automatically generated character using the [randomuser.me](https://randomuser.me) API.
- **User Login**: Users log in using their email without passwords (for this PoC).
- **Role-Based Access Control (RBAC)**: Restricts access based on user roles.
- **REST API Endpoint**: Allows role assignment based on email or name.

### Plugin Structure

```
cool-kids-network/
├── assets/
│   ├── index.php
│   ├── admin/
│   │   ├── css/
│   │   ├── font/
│   │   ├── img/
│   │   ├── js/
│   │   └── index.php
│   ├── public/
│   │   ├── css/
│   │   ├── font/
│   │   ├── img/
│   │   ├── js/
│   │   └── index.php
├── src/
│   ├── index.php
│   ├── Enqueue.php  // Manages script and style loading
│   ├── Shortcodes.php  // Handles shortcodes
│   ├── UserManager.php  // Manages user creation and login
│   ├── RoleManager.php  // Handles role-based access and API
│   ├── API.php  // Registers REST API endpoints
│   ├── Ajax.php  // Handles AJAX requests for loading more users in user-view.php
│   └── Utils.php  // Utility functions
├── templates/
│   ├── index.php
│   ├── guest-view.php // Welcome template for guest users
│   ├── login.php // Login template
│   ├── signup.php // Signup template
│   └── user-view.php // Profile template with user list
├── tests/
│   ├── index.php
│   ├── APITest.php
│   └── bootstrap.php
├── .gitignore
├── LICENSE
├── composer.json
├── composer.lock
├── cool-kids-network.php  // Main plugin file
├── index.php
├── phpcs.xml
└── phpunit.xml
```

## Technical Decisions & Justifications


### Utility Functions (`src/Utils.php`)  

The `Utils` class serves as a centralized helper for commonly used functions across the plugin. Keeping these functions in a separate utility class promotes code reusability and maintainability, and scalability.  

**Key Responsibilities:**  
- **Data Formatting:** Provides helper functions for formatting user data consistently.  
- **Validation:** Includes methods for validating input data before processing.  
- **API Requests:** Handles external API requests (fetching random user data from RandomUser API).  
- **General Helpers:** Contains miscellaneous utility functions to keep other classes focused on their primary responsibilities.  

**Justification:**  
- **Separation of Concerns:** Avoids cluttering core classes with helper methods.  
- **Code Reusability:** Ensures functions can be reused without duplication.  
- **Improved Maintainability and Scalability:** Centralized logic makes debugging and extending functionalities easier.


### Shortcodes (`src/Shortcodes.php`)  

The `Shortcodes` class is responsible for registering and managing the plugin's shortcodes.

**Key Responsibilities:**  
- **Shortcode Registration:** Initializes shortcodes for various views.  
- **Template Rendering:** Loads the corresponding template files from the `/templates/` directory.  
- **Conditional Display:** Adjusts output based on the user’s login status.  

**Implemented Shortcodes:**  
- `[coolkids_home]` → Displays either the user profile (`templates/user-view.php`) or the guest view (`templates/guest-view.php`).  
- `[coolkids_login]` → Shows the login form (`templates/login.php`) unless the user is already logged in.  
- `[coolkids_signup]` → Displays the signup form (`templates/signup.php`) unless the user is already logged in.  

**Usage Guidelines:**  
At the moment, to ensure proper functionality, the website owner or admin should:  
- Add `[coolkids_home]` to the homepage to display user profiles or the guest view. 
- Create a login page with `/signup/` as the page slug and use `[coolkids_signup]` to display the new user registration form.  
- Create a login page with `/login/` as the page slug and use `[coolkids_login]` to display the login form. 

**Template Handling:**  
The `get_template()` method retrieves and renders template files stored in the `/templates/` directory. It ensures that:  
- The correct file is loaded based on the requested template name.  
- Output buffering (`ob_start()`) is used to capture and return the rendered content. 

**Justification:**  
- **User-Friendly Embedding:** Allows admins to integrate the plugin’s functionality without modifying theme files.  
- **Code Modularity:** Keeps template logic separated for better maintainability.  
- **Dynamic Content Handling:** Ensures the correct templates are displayed based on user roles and login state.  


### User Management (`src/UserManager.php`)  

The `UserManager` class handles user authentication, login and signup processing, redirection for logged-in users, and hiding the WordPress admin bar for specific user roles.  

**Key Responsibilities:**  
- **User Signup Handling:** Processes signup requests, verifies nonces, sanitizes input, and redirects users accordingly.  
- **User Login Handling:** Authenticates users based on their email and verifies nonces.  
- **Redirecting Logged-in Users:** Ensures logged-in users cannot access the signup and login pages.  
- **Hiding Admin Bar:** Prevents users with specific "Cool Kid" roles from seeing the WordPress admin bar.  

**Implemented Features:**  

- **Signup Handling (`handle_signup`)**  
  - Listens for signup form submissions.  
  - Verifies security nonces to prevent CSRF attacks.  
  - Calls `Utils::authenticate_email()` to process authentication.  
  - Stores success or failure messages using WordPress transients.  
  - Redirects users back to the `/signup/` page.  

- **Login Handling (`handle_login`)**  
  - Listens for login form submissions.  
  - Verifies security nonces to prevent CSRF attacks.  
  - Calls `Utils::authenticate_user()` to handle authentication.  

- **Logged-in User Redirection (`redirect_logged_in_users`)**  
  - Prevents logged-in users from accessing the `/signup/` and `/login/` pages.  
  - If a logged-in user attempts to visit these pages, they are redirected to the homepage.  

- **Admin Bar Removal (`remove_admin_bar_for_coolkid_roles`)**  
  - Hides the WordPress admin bar for users with the roles `cool_kid`, `cooler_kid`, and `coolest_kid`.  

**Justification:**  
- **Security & Data Integrity:** Uses nonce verification and sanitization to protect user authentication.  
- **User Experience:** Ensures logged-in users are redirected properly and do not access unnecessary pages.  
- **Cleaner UI:** Hides the WordPress admin bar for specific roles to keep the frontend clean.


### Role Management (`src/RoleManager.php`)  

The `RoleManager` class is responsible for registering custom user roles in WordPress. These roles define different permission levels for users within the Cool Kids Network.  

**Key Responsibilities:**  
- **Custom Role Registration:** Defines and registers three user roles: `Cool Kid`, `Cooler Kid`, and `Coolest Kid`.  
- **Role-Based Capabilities:** Assigns specific capabilities to each role.  

**Implemented Features:**  

- **Role Registration (`register_roles`)**  
  - Defines three custom user roles with varying permissions:  
    - **Cool Kid (`cool_kid`)**: Can only read content.  
    - **Cooler Kid (`cooler_kid`)**: Can read content and list users.  
    - **Coolest Kid (`coolest_kid`)**: Has the same capabilities as `Cooler Kid`, allowing for future expansion.  
  - Uses `add_role()` to register these roles with WordPress.  

**Justification:**  
- **Custom Access Control:** Defines distinct permission levels for different user types.  
- **Scalability:** Allows easy expansion of roles and capabilities in future updates.  
- **WordPress Best Practices:** Uses `add_role()` for proper integration with WordPress' role management system. 


### AJAX Handling (`src/Ajax.php`)  

When user logged-in, if they can view users, they will see a list of other members. Performance wise, it only load 12 users initially and provide a load more button to display another 12 users (and so on). This `Ajax` class is responsible for handling AJAX-based interactions to enable dynamic user loading without requiring full-page refreshes.  

**Key Responsibilities:**  
- **AJAX Action Registration:** Hooks into WordPress' AJAX system to enable secure requests.  
- **User Pagination:** Fetches and returns user data in batches to optimize performance.  
- **Access Control:** Ensures that only authorized users can load more user profiles.  

**Implemented Features:**  

- **AJAX Initialization (`init`)**  
  - Registers the `load_more_users` AJAX action for both logged-in users (`wp_ajax_`) and guests (`wp_ajax_nopriv_`).  

- **Load More Users (`load_more_users`)**  
  - **Security Check:** Uses `check_ajax_referer()` to validate the request.  
  - **Permission Validation:** Only users with `cooler_kid` or `coolest_kid` roles can load more users.  
  - **User Query:** Retrieves the next batch of users based on the provided `offset`.  
  - **HTML Generation:** Uses `Utils::generate_user_card_html()` to format the user list.  
  - **Pagination Handling:** Returns `has_more: true` if more users are available.  

**Justification:**  
- **Efficient User Loading:** Prevents unnecessary full-page reloads by fetching data dynamically.  
- **Secure Requests:** Ensures only authorized users can access user data.  
- **Optimized Performance:** Uses pagination to minimize database queries and improve UX.  

### REST API Handling (`src/API.php`)  

The `API` class is responsible for managing REST API endpoints related to user role updates. It allows administrators to change user roles based on specific identifiers such as email or custom meta fields.  

### **Key Responsibilities:**  
- **REST API Route Registration:** Defines and registers the `coolkids/v1/update-role/` endpoint.  
- **User Role Management:** Allows role updates for users identified by email or user meta.  
- **Permission Enforcement:** Restricts access to users with the `manage_options` capability.  

### **Implemented Features:**  

#### **1. API Initialization (`init`)**  
- Hooks into `rest_api_init` to register the custom REST API endpoint.  

#### **2. Route Registration (`register_routes`)**  
- Defines the `coolkids/v1/update-role/` endpoint with:  
  - `POST` method.  
  - `update_user_roles` callback for handling requests.  
  - `check_permissions` callback to restrict access.  

#### **3. User Role Update (`update_user_roles`)**  
- **Security & Validation:**  
  - Ensures request contains a valid user array.  
  - Allows only predefined roles (`cool_kid`, `cooler_kid`, `coolest_kid`).  
- **User Lookup:**  
  - Searches by **email** first.  
  - If no match, queries the `cool_kids_character` meta field.  
  - Compares the identifier against email, first name, and last name.  
  - Uses caching (`wp_cache_get` & `wp_cache_set`) for performance.  
- **Role Assignment:**  
  - Updates the user's role if found.  
  - Returns success or failure messages for each request.  

#### **4. Permission Check (`check_permissions`)**  
- Only users with `manage_options` can update roles.  

### **Justification & Benefits:**  
- **Scalability:** Efficient meta queries and caching improve performance.  
- **Security:** Restricts API access to administrators only.  
- **Flexibility:** Supports multiple user identification methods.  
- **Performance:** Caching minimizes repeated database queries.  

### Enqueuing Styles and Scripts (`src/Enqueue.php`)

The `Enqueue` class is responsible for managing the loading of styles and scripts in the Cool Kids Network plugin. It ensures that assets are only loaded when necessary, optimizing performance and reducing unnecessary resource usage.

### **Key Responsibilities:**  
- **Conditional Asset Loading:** Styles and scripts are only enqueued when the relevant shortcodes (`[coolkids_home]`, `[coolkids_login]`, `[coolkids_signup]`) are present on the page.  
- **Efficient Style Management:** Registers and enqueues styles while injecting inline styles dynamically based on the active shortcodes.  
- **Optimized Script Loading:** Registers scripts with a `defer` strategy and enqueues them only when required.  
- **AJAX Support:** Implements `wp_localize_script` to pass necessary data (like `admin-ajax.php` URL and nonce) for secure AJAX interactions.

### **Implementation Details:**  

#### **1. Conditional Style Enqueueing**  
- The plugin registers the main stylesheet `styles.css`.  
- Before enqueuing, it checks if any of the relevant shortcodes exist in the page content.  
- If the `[coolkids_home]` or authentication-related shortcodes are detected, styles are enqueued.  
- Inline styles are dynamically added based on the required UI elements for login, signup, and user listings.

#### **2. Conditional Script Enqueueing**  
- The script `load-more-users.js` is only loaded when the `[coolkids_home]` shortcode is present.  
- The script is registered with `defer` to improve loading performance.  
- `wp_localize_script` is used to provide AJAX-related data, ensuring secure communication with WordPress.

### **Justification & Benefits:**  
- **Performance Optimization:** Prevents unnecessary asset loading, reducing page load times.  
- **Better User Experience:** Ensures that required styles and scripts are available when needed without affecting other pages.  
- **Improved Maintainability:** Centralized management of styles and scripts makes future updates easier.  
- **Enhanced Security:** Uses WordPress-native functions like `wp_localize_script` with nonce verification for AJAX requests.  

This approach ensures that the Cool Kids Network plugin remains lightweight and performant.


## Styling (`assets/public/css/styles.css`)

### **Implementation Details:**
The styling approach in `styles.css` follows a **utility-first CSS strategy** to enhance maintainability, efficiency, and consistency across the project. Instead of writing repetitive, component-specific CSS, utility classes are used to handle common styling needs such as spacing, positioning, alignment, and responsiveness. This approach keeps the CSS lean and avoids unnecessary duplication.

Additionally, a **fluid (liquid) CSS approach** is employed where possible, using `clamp()`, `calc()`, and viewport-based units to ensure styles dynamically adapt across different screen sizes. This technique allows for smooth scaling of typography, spacing, and border-radius values, creating a more responsive and visually balanced design without the need for multiple media queries.

### **Justification & Benefits:**
- **Efficiency:** Prevents redundant CSS rules by encouraging reusable, scalable utility classes.  
- **Performance:** Reduces unnecessary CSS bloat, ensuring faster page loads.  
- **Consistency:** Standardized spacing, font sizing, and layout behavior across components.  
- **Maintainability:** Easier to update and extend without affecting multiple elements.  
- **Developer Experience:** Simplifies class structure and speeds up development by using pre-defined utilities rather than writing new CSS for each component.

By leveraging this approach, the project maintains a **high level of code reusability and scalability**, ensuring the styling remains manageable as the application evolves.


## Meeting the User Stories

| User Story | Implementation |
|------------|----------------|
| **Sign-up & Character Creation** | A form collects the email, and a character is created with the RandomUser API. |
| **Login & Character Data View** | Users log in using their email and can view their character details. |
| **Cooler Kid Role Access** | Users with this role can see all users’ names and countries. |
| **Coolest Kid Role Access** | Users with this role can see all users’ names and countries, as well as emails and roles. |
| **Admin Role Assignment API** | A secure REST API endpoint allows modifying user roles. |

## Future Enhancements
- Implement proper authentication for login (passwords, OAuth).
- Add frontend improvements for better user experience.
- Improve database performance with indexing for large user bases.
- Implement email verification during sign-up.

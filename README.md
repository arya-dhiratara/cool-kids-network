# Cool Kids Network  

Cool Kids Network is a WordPress plugin that implements a user management system, serving as a proof of concept (PoC) for a WordPress Developer Technical Assessment.  

## **Features**  
- **User Registration**: Users can sign up using their email, and a character is automatically created for them.  
- **Random Character Generation**: Upon registration, the plugin generates a random first name, last name, and country using [randomuser.me](https://randomuser.me) API.  
- **User Login**: Users can log in using only their email (no password required for this PoC).  
- **User Roles**: Three roles exist in the system:  
  - `cool_kid` (default): Can view only their own character data.  
  - `cooler_kid`: Can view other user's name and country, but not email and role.  
  - `coolest_kid`: Can view other user's name, country, email, and role.  
- **Role Management via API**: The website admin can change user roles using a secure REST API. 

## **Usage Guidelines**  

To ensure proper functionality, currently the website owner or admin should:  
1. Add `[coolkids_home]` to the homepage to display user profiles or the guest view.  
2. Create a signup page with `/signup/` as the page slug and use `[coolkids_signup]` to display the new user registration form.  
3. Create a login page with `/login/` as the page slug and use `[coolkids_login]` to display the login form.  

---

## **REST API: Changing User Roles**  

The plugin provides a protected REST API endpoint to update user roles dynamically.  

### **Endpoint:**  
`POST /wp-json/coolkids/v1/update-role/`  

### **Request Parameters:**  
The request should include one of the following identifiers in the JSON body:  

| Parameter  | Type   | Required | Description |
|------------|--------|----------|-------------|
| `users`    | array  | Yes      | An array of user objects to update. Each object should contain `identifier` and `role`. |
| `identifier` | string | Yes    | Can be the email, the first name, last name, or full name of the user. |
| `role`      | string | Yes    | The new role to assign (`cool_kid`, `cooler_kid`, `coolest_kid`). |

### **Authentication:**  
Requests must be authenticated using an **Application Password** from a WordPress user profile. 

### **Example Request (cURL):**  

#### **Single User Update**
```sh
curl -X POST https://domain.example/wp-json/coolkids/v1/update-role \
     -u 'yourusername:your-application-password' \
     -H "Content-Type: application/json" \
     -d '{
           "users": [
             {
               "identifier": "user@example.com",
               "role": "cooler_kid"
             }
           ]
         }'
```

#### **Multiple Users Update**
```sh
curl -X POST https://domain.example/wp-json/coolkids/v1/update-role \
     -u 'yourusername:your-application-password' \
     -H "Content-Type: application/json" \
     -d '{
           "users": [
             { "identifier": "first name", "role": "cooler_kid" },
             { "identifier": "last name", "role": "cooler_kid" },
             { "identifier": "full name", "role": "coolest_kid" },
             { "identifier": "user@example.com", "role": "cooler_kid" }
           ]
         }'
```


### **Using Postman**  
1. **Open Postman** and create a new `POST` request to:  
   ```
   https://domain.example/wp-json/coolkids/v1/update-role/
   ```
2. **Go to the "Authorization" tab**, select **Basic Auth**, and enter your **WordPress username** and **Application Password**.  
3. **Go to the "Body" tab**, select **raw**, and set the format to `JSON`.  
4. **Enter the request payload**:  

   **Single User:**
   ```json
   {
     "users": [
       {
         "identifier": "user@example.com",
         "role": "cooler_kid"
       }
     ]
   }
   ```

   **Multiple Users:**
   ```json
   {
     "users": [
       { "identifier": "first name", "role": "cooler_kid" },
       { "identifier": "last name", "role": "cooler_kid" },
       { "identifier": "full name", "role": "coolest_kid" },
       { "identifier": "user@example.com", "role": "cooler_kid" }
     ]
   }
   ```
5. **Click "Send"** to execute the request. If successful, you’ll receive a response confirming the role change.
# Gestion Stock Magasin

A comprehensive stock management system built with PHP and JavaScript, featuring real-time inventory tracking, user authentication, and movement history.

## 🌟 Features

- **Product Management**
  - Add, edit, and delete products
  - Real-time stock level monitoring
  - Category-based organization
  - Price and quantity tracking
  - Low stock alerts

- **Stock Movement Tracking**
  - Record stock entries and exits
  - Movement history with detailed logs
  - Filter movements by date and type
  - User attribution for each movement

- **User Authentication**
  - Secure login system
  - Session management
  - User role-based access control

- **Category Management**
  - Organize products by categories
  - Add and manage product categories

## 🛠️ Technical Stack

- **Frontend**
  - JavaScript (ES6+)
  - Bootstrap 5
  - Modern DOM manipulation
  - Async/Await for API calls

- **Backend**
  - PHP
  - MySQL Database
  - RESTful API architecture

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for local development)

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/gestion_stock_magasin.git
   ```

2. Set up your web server:
   - Place the project in your web server's root directory (e.g., `htdocs` for XAMPP)
   - Ensure the web server has write permissions for the project directory

3. Database Setup:
   - Create a new MySQL database
   - Import the database schema from the `database` directory
   - Update database credentials in `config/database.php`

4. Configure the application:
   - Update the base URL in configuration files if needed
   - Ensure all required PHP extensions are enabled

## 📁 Project Structure

```
gestion_stock_magasin/
├── api/            # API endpoints
├── assets/         # Static assets (JS, CSS)
├── auth/           # Authentication system
├── categories/     # Category management
├── config/         # Configuration files
├── database/       # Database schema and migrations
├── movements/      # Stock movement management
├── products/       # Product management
└── index.php       # Main entry point
```

## 🔧 Configuration

1. Database Configuration:
   - Navigate to `config/database.php`
   - Update the following variables:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'your_database');
     ```

2. Application Settings:
   - Update base URL and other settings in `config/config.php`

## 💻 Usage

1. Access the application through your web browser:
   ```
   http://localhost/gestion_stock_magasin
   ```

2. Login with your credentials:
   - Default admin credentials (if applicable):
     - Username: admin
     - Password: admin123

3. Navigate through the different sections:
   - Products: Manage inventory items
   - Movements: Track stock entries and exits
   - Categories: Organize products
   - Users: Manage user accounts

## 🔐 Security Features

- Password hashing
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License 

## 👥 Authors

- Bendada Mohamed, Hicham Benmina - Initial work

## 🙏 Acknowledgments

- Bootstrap for the UI components
- All contributors who have helped shape this project

## 📞 Support

For support, please open an issue in the GitHub repository or contact the maintainers. 

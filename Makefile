# Makefile for Zender SaaS Platform
# Provides setup and management commands

.PHONY: help permissions db-setup upload install-wizard clean

help:
	@echo "Available targets:"
	@echo "  permissions      Set required permissions for uploads/ and system/ directories"
	@echo "  db-setup         Print instructions for MySQL database setup"
	@echo "  upload           Print instructions for uploading files to your server"
	@echo "  install-wizard   Print instructions to run the Zender installation wizard"
	@echo "  clean            Remove cache/temp files (if any)"

permissions:
	@echo "Setting permissions for uploads/ and system/ directories..."
	chmod -R 775 uploads
	chmod -R 775 system

# This target does not actually create a database, but prints instructions
# because DB creation is usually done via MySQL CLI or control panel
db-setup:
	@echo "To create the database, run:"
	@echo "  CREATE DATABASE zender_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
	@echo "  CREATE USER 'zender_user'@'localhost' IDENTIFIED BY 'your_password';"
	@echo "  GRANT ALL PRIVILEGES ON zender_db.* TO 'zender_user'@'localhost';"
	@echo "  FLUSH PRIVILEGES;"

upload:
	@echo "Upload all files and folders from the Install directory to your server's web root or desired subdirectory."
	@echo "Use an FTP client (like FileZilla), cPanel File Manager, or rsync if you have SSH access."
	@echo "Ensure hidden files (.htaccess, .nginx.conf) are uploaded."

install-wizard:
	@echo "In your browser, visit your domain or subdirectory where you uploaded Zender."
	@echo "You should see the installation wizard."
	@echo "Follow the prompts to enter site info, database credentials, and admin account details."
	@echo "Complete the installation and log in as admin."

clean:
	@echo "No temporary files to clean by default. Add custom clean-up steps if needed."

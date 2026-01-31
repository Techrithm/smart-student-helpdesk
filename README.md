# Smart Student Helpdesk

## 📌 Project Setup Guide (For Teammates)

Follow these steps to run the project on your local machine.

---

## ✅ Requirements

- XAMPP installed (Apache + MySQL)
- Any code editor
- Web browser

---

## 📥 1. Clone the Repository

Open terminal / CMD and run:

git clone https://github.com/Techitm/smart-student-helpdesk.git

Then move the folder into:

C:\xampp\htdocs

So the path becomes:

C:\xampp\htdocs\smart-student-helpdesk

---

## 🚀 2. Start XAMPP

Open XAMPP Control Panel:

- Start **Apache**
- Start **MySQL**

---

## 🗄️ 3. Import Database

1. Open browser and go to:

http://localhost/phpmyadmin

2. Click **New**
3. Create database:

smart_student_helpdesk

4. Select the new database
5. Click **Import**
6. Choose the file:

smart_student_helpdesk.sql  
(from the project folder)

7. Click **Go**

Database is now restored.

---

## ⚙️ 4. Update Database Config (if needed)

Open your PHP config / connection file and make sure:

host = localhost  
user = root  
password = (empty)  
database = smart_student_helpdesk  

---

## ▶️ 5. Run the Project

Open browser:

http://localhost/smart-student-helpdesk/

---

## 🔁 For Making Changes

After editing code:

git add .
git commit -m "your message"
git push

---

## 📎 Notes

- XAMPP itself is NOT included in GitHub
- Database is provided as .sql file
- Everyone must import DB locally
- Don’t push XAMPP folders or personal configs

---

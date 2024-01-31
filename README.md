# Online Quiz Web Application #

<p align="center">
   <strong>This application can be used to set up different quizzes for students, created by trainers/administrators.</strong>
</p>

<details>
  <summary><strong>➡️ Screenshots (Application)</strong></summary><br/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/home_not_connected.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/login.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/register.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/home_connected_no_recent_quiz.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/profile.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/list_quiz.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/quiz_question.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/quiz_question_with_progressbar.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/quiz_result.png" width="280" target="_blank"/>
</details>

<details>
  <summary><strong>➡️ Screenshots (Admin)</strong></summary><br/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/home_admin.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/stats_admin_list_promo.png" width="280" target="_blank"/>
  <img src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/screenshots/stats_admin_list_users.png" width="280" target="_blank"/>
</details>

### Features 🚀

- 🌌 **Modern, fluid application**

- 🌐 **Authentication & role management**

- 🔄 **Content categorization**

- ✏️ **Create quizzes with different types of questions (MCQ, Single choice)**

- 📊 **Advanced statistics for both users and trainers**

- 🌐 **Available in 5️⃣ different languages**

- ⚙️ **Management panel for instructors/administrators**
  

## Prerequisites for installation 🛠️

- PHP 8.2.X
- MariaDB 10.10.X
- Symfony 7.0.1 [(Download)](https://symfony.com/download)
- Composer [(Download)](https://getcomposer.org/download/)

## How to Run the Project ▶️

1. Clone this repository to your local machine.
2. Import SQL file.
3. Run the `composer i` command in the project root.
4. Modify your database connection information. (`.env`)
5. Run fixtures to get a dataset and an administrator account. (`php bin/console doctrine:fixtures:load`) (_Admin Account credentials will be displayed at the end of command execution_)
6. Go to the management panel, and put yourself in one or more promotions to get the quizzes.
7. Run the `symfony server:start -d` command

## Authors ✨

[@KoZeuh](https://github.com/KoZeuh)
  
## License 📄

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.


# Application Web de Quiz en ligne #

<p align="center">
   <strong>This application can be used to set up different quizzes for students, created by trainers/administrators.</strong>
</p>

<details>
  <summary><strong>➡️ Screenshots (SOON)</strong></summary>
  <br/>
  <img align="left" src="https://github.com/KoZeuh/Quiz-Project-ESGI/blob/main/DOCUMENTS/schema.svg" width="280" target="_blank"/>
  <br/>
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
5. Run fixtures to get a dataset and an administrator account. (`php bin/console doctrine:fixtures:load`) (_Account credentials will be displayed at the end of command execution_)
6. Go to the management panel, and put yourself in one or more promotions to get the quizzes.

## Authors ✨

[@KoZeuh](https://github.com/KoZeuh)
  
## License 📄

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.


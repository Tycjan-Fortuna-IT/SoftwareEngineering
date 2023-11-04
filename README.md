***

# Getting Started



## <ins>**1. Prerequisites:**</ins>
- [x] PHP 8.2.X
- [x] Composer 2.5.X
- [x] PostgreSQL

Linux or windows doesn't matter.
## <ins>**2. Downloading the repository:**</ins>

Start by cloning the repository with `git clone https://github.com/Tycjan-Fortuna-IT/SoftwareEngineering.git`.

## <ins>**3. Installing dependencies:**</ins>

Run `composer install` to install all dependencies.

## <ins>**4. Setting up the database:**</ins>

Create a new database and set the database credentials in the `.env` file. Then run `php artisan migrate` to create the database tables.

### <ins>**5. Run locally:**</ins>

Run `php artisan serve` to start the local development server.

### <ins>**5. Use working test server:**</ins>

Here will be provided later.

### Other

#### **Commit Convention:** All commits should be in English and include a descriptive message that provides clear and concise information about the changes made.

#### **Branch Name Convention:** Branch names should be informative and reflect the purpose of the branch. Follow a convention like `feature_feature_name` or `bugfix_issue_description` to provide context.

#### **Pull Request (PR) Structure:** PRs should include a clear and concise description, summarizing the purpose and changes. The PR body should provide detailed information, context, and testing steps. Each PR should be linked to the related issue (if applicable).

**Branch Management:**

- **Master Branch:** The `master` branch should always represent the production-ready code. No direct commits or pushes to this branch are allowed. All changes to `master` should be made through a Pull Request (PR) after thorough review and testing.

- **Development (Dev) Branch:** The `dev` branch is the main development branch. It serves as a staging area for ongoing work and is where feature branches are merged for integration and testing. Only stable and tested code should be merged into the `dev` branch.

Please ensure that the `master` and `dev` branches are kept clean, with only production-ready and thoroughly tested code making its way into the `master` branch. Development work and feature testing should primarily take place in feature branches created from `dev`.

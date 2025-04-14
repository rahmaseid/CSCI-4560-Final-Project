# Personal Budget App

####

Our Personal Budget System is a digital tool made to help users efficiently track, manage, and plan their finances. It can record transactions, categorize expenses, set financial goals, and provide insightful reports to promote better budgeting habits.


## Tech Stack

**Database:** MySQL WorkBench

**Backend:** Java

**Frontend:** HTML/CSS, React.js, Vue.js

**Deployment:** Docker

**ERD Modeling:** Lucidchart



## Key Features

- Expense Categorization: Track expenses by categories
- Goal-Based Savings Tracking: Users can set and monitor savings goals
- Bill Reminder & Payment Scheduling: Notifies users of upcoming bills and allows automated payments
- Expense Forecasts: Predicts future spending based on past transactions
- Cash Flow Data: Generates visual reports comparing income and expenses


## Challenges Encountered & Viable Solutions
- Challenge: Finding relevant datasets
   - Solution: Using Mockaroo for data generation
- Challenge: Cleaning and validating large datasets
   - Solution: Implementing data cleaning scripts


## Schema Structure
```plaintext
Personal Budget App
├── Users
|   ├── user_id
|   ├── email
|   ├── username
|   ├── password
|   ├── data_joined                 
|
├── Income
|   ├── income_id
|   ├── user_id
|   ├── income_source
|   ├── income_amount
|   
├── Categories
|   ├── categories_id
|   ├── categories_name
|
├── Savings Goals
|   ├── user_id
|   ├── goals_id
|   ├── categories_id
|   ├── goal_name
|   ├── target_amount
|   ├── current_amount
|
├── Transactions
|   ├── user_id
|   ├── categories_id
|   ├── transaction_id
|   ├── transaction_date
|   ├── amount
|   ├── location
|
├── Bills
|   ├── bill_id
|   ├── user_id
|   ├── bill_name
|   ├── bill_amount
|   ├── due_date
|   ├── payment_date
|   ├── paid_status
|
├── Bill_Reminder
|   ├── bill_id
|   ├── reminder_id
|   ├── reminder_date
|
└── README.md
```


## Authors

- [Jennifer Nyguen](https://github.com/Jennygit03)
- [Tyler Roediger](https://github.com/tar3qMT)
- [Rahma Seid](https://github.com/rahmaseid)


## 🔗 Links
- [GitHub Repository](https://github.com/Jennygit03/CSCI-4560)
- [Presentation](https://docs.google.com/presentation/d/1nu7YUajwIhhEvWLHyYrKfjP0L3KbYELVvl8LDzjkTFg/edit?usp=sharing)

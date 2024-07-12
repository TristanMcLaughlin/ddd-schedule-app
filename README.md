A simple scheduling app to help move Jira tickets into the schedule so I don't have to do much scheduling work.

- Grabs bank holidays
- Grabs Jira team members and seeds unassigned people
- Grabs Jira projects and the myriad of custom fields and assignees
- Adds some custom business logic and employs the strategy pattern to populate date periods for assignees
- Imports backlog tickets with the highest priority visible in the schedule
- All items fallback to unassigned to help keep on top of the otherwise unorganised backlog (Since we work using kanban)
- API tokens and IDs are all secrets
- Uses Docker so I don't have to use PHP locally cause I prefer keeping everything versioned in containers
- In deployment you'd hook into an auth microservice with your validated clients list and store the API key as a secret
- You'd just add a prod docker yaml file to deploy which grabs the secrets from whatever service you use (Probs github secrets or if using CICD the secrets stored in there)

- yay


Note:
I went half DDD on this one and made some kind of weird hybrid where Eloquent migrations and the front end are split apart from any layer. I haven't worked on cleaning up the directories and moving them from a typical Laravel structure so much, just wanted to get this prototype out the door and usable so I could use my time more productively.

Node is not containerised yet, but it uses v22. I use nvm locally for version switching when needed.

Pictures

Light mode - All projects
<img width="1200" alt="Screenshot 2024-07-12 at 17 52 56" src="https://github.com/user-attachments/assets/362e4fa8-997b-475c-abde-b21e8bcaf03e">

Dark mode - All projects
<img width="1200" alt="Screenshot 2024-07-12 at 17 56 13" src="https://github.com/user-attachments/assets/896ddbc4-2e8b-47d2-9de3-66b14defda2e">

Single project view
<img width="1200" alt="Screenshot 2024-07-12 at 17 54 07" src="https://github.com/user-attachments/assets/ee0efe6c-983e-4e70-b36c-efef4acf8454">


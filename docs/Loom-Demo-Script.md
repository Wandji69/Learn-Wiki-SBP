# Loom Demo Script

1. Open `/app/register` and register as a normal user.
2. Login and show token-based flow lands on Courses page.
3. Logout, login as seeded admin (`admin@example.com` / `password`).
4. Create a course from the admin section.
5. Logout, login as normal user, list courses and enroll.
6. Navigate to `/app/appointments`, book an appointment.
7. Show appointments list displays only this user’s records.
8. In Postman, call `GET /api/auth/me` and `GET /api/appointments` with JWT.
9. Briefly show repository/service structure in code.

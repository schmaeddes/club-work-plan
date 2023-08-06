# ToDo Stuff

## 📚 Features

### Frontend
- [ ] Save Name correct in db via frontend

### Admin Page
- [x] Edit Event
- [x] Edit Duty
- [x] Deletable duties with row action
- [ ] Deletable duties with bulk action
- [ ] Deletable events via row action (with linked duties)
- [ ] Deletable events via bulk action (with linked duties)
- [ ] Deletable events (with linked duties)
- [ ] Show Dates formatted from settings
      get_option('date_format'), strtotime($dutyData->endTime);

## 🐞 Bugs

- [ ] End time of duty can be empty - but gets stored with 00:00:00

## Chore

- [ ] Sanitze or filter input for user inputs (Clean the input)
      https://developer.wordpress.org/apis/security/sanitizing/
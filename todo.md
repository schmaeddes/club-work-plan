# ToDo Stuff

## 📚 Features

### Frontend
- [x] Save Name correct in db via frontend

### Admin Page
- [x] Edit Event
- [x] Edit Duty
- [ ] Show admin_notice if event or duty is edited
- [x] Show admin_notice if event or duty is deleted
- [ ] Show Alert if event should be deleted because duties will also be deleted 
- [x] Deletable duties with row action
- [ ] Deletable duties with bulk action
- [x] Deletable events via row action (with linked duties)
- [ ] Deletable events via bulk action (with linked duties)
- [ ] Show Dates formatted from settings
      get_option('date_format'), strtotime($dutyData->endTime);

## 🐞 Bugs

- [ ] End time of duty can be empty - but gets stored with 00:00:00
- [x] Dutys are not deletable

## Chore

- [ ] Sanitze or filter input for user inputs (Clean the input)
      https://developer.wordpress.org/apis/security/sanitizing/
      
      WP_DEBUG und WP_DEBUG_DISPLAY in der wp-config an
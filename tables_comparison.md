# Comparison: tables.tao.ai vs tables.im

Generated: 2025-10-10

## Summary
- **Modified Files:** 3
- **Files Only in tables.im:** 89
- **Files Only in tables.tao.ai:** 3

---

## Modified Files (Exist in Both, but Different)

1. `.claude/settings.local.json`
2. `.env.tables.im`
3. `.htaccess`

---

## Files Only in tables.tao.ai (Missing in tables.im)

1. `.env`
2. `.env.example`
3. `.env.tables.tao.ai`
4. `.gitignore`

---

## Files Only in tables.im (New/Additional Files)

### API Endpoints (8 files)
1. `api/accept_meet.php`
2. `api/check_meet_acceptance.php`
3. `api/check_meet_requests.php`
4. `api/latch.php`
5. `api/remove_seat.php`
6. `api/request_meet.php`
7. `api/seat_add.php`
8. `api/seat_manage.php`
9. `api/update_seat.php`

### Documentation (5 files)
1. `bkup_CLAUDE.md`
2. `CLAUDE.md`
3. `DEBUG_CONTROL_VERIFICATION.md`
4. `DELETE_DEBUG_SUMMARY.md`
5. `DEPLOYMENT.md`
6. `SEAT_MANAGEMENT.md`

### Main Application Files (14 files)
1. `clear_verification_cache.php`
2. `client_utility.php`
3. `debug_logs.php`
4. `debug_utility.php`
5. `email_notifications_config.php`
6. `meets.php`
7. `reset_verification.php`
8. `seat_utility.php`
9. `sharing.js`
10. `styles.css`
11. `table_utility.php`

### CSS (1 file)
1. `css/style.css`

### Test Files (8 files)
1. `test/` (directory)
2. `test_api_debug_control.php`
3. `test_back_button.php`
4. `test.csv`
5. `test_debug_control.php`
6. `test_debug.php`
7. `test-theme.html`
8. `theme-test.html`

### Log Files (2 files)
1. `email_debug.log`
2. `logs/app_errors.log`

### Verification Code Logs (37 JSON files)
Located in `logs/verification/`:
1. code_AHBRBZ.json
2. code_ALFWMY.json
3. code_AMVGHI.json
4. code_AXPKMZ.json
5. code_BWYYBM.json
6. code_CNHXEB.json
7. code_DEEEXT.json
8. code_DFWXNL.json
9. code_DTBANY.json
10. code_DXLQVD.json
11. code_DXVLMP.json
12. code_ERLDAB.json
13. code_FFJVDE.json
14. code_GIYXUQ.json
15. code_GNITIZ.json
16. code_GUBALD.json
17. code_IPKYDL.json
18. code_ISHVTO.json
19. code_IZUPQP.json
20. code_JUIGHD.json
21. code_LNCAAW.json
22. code_LTVDAN.json
23. code_MDFJWU.json
24. code_MFJXKX.json
25. code_MWBHDJ.json
26. code_NENWIV.json
27. code_NKBIDU.json
28. code_NQGEEA.json
29. code_NRDXYP.json
30. code_PULNGI.json
31. code_QBUSWL.json
32. code_QDHUBT.json
33. code_QHKYRF.json
34. code_QXNAMD.json
35. code_RKWPAR.json
36. code_TEQGJJ.json
37. code_TNGQKM.json
38. code_UFAASV.json
39. code_UIPASG.json
40. code_VINNEB.json
41. code_VRHAWF.json
42. code_VSARUR.json
43. code_WHFWZX.json
44. code_WMJQQH.json
45. code_WNHEUJ.json
46. code_WTHSCW.json
47. code_WUBUTR.json
48. code_WXASYK.json

---

## Key Differences Analysis

### tables.im Has:
- **Meet/Seat Management Features** (9 new API endpoints)
- **Enhanced Debugging Tools** (debug utilities, logs)
- **Email Notification System**
- **Verification System** (with 48 verification code logs)
- **Additional Utility Files** (client, seat, table utilities)
- **More Documentation** (6 markdown files)
- **Test Files** (8 test-related files)

### tables.tao.ai Has:
- **Environment Configuration** (.env files)
- **Git Configuration** (.gitignore)

### Modified Configuration:
- **Different Claude settings**
- **Different .htaccess rules**
- **Different .env.tables.im configuration**

---

## Recommendation

**tables.im** appears to be the more developed/production version with:
- Meet and seat management functionality
- Email notifications
- Enhanced debugging and logging
- More complete feature set

**tables.tao.ai** appears to be an older or different environment/configuration.

If you want to sync features from tables.im to tables.tao.ai, you would need to copy over the API files, utilities, and documentation.

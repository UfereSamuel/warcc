# RCC Staff Management System - Testing Guide

## 🧪 **Test Accounts Created Successfully**

The system now has test staff accounts with sample data for comprehensive testing of the Staff Dashboard & Attendance System.

## 🚀 **Quick Start Testing**

### **1. Access Test Accounts Page**
Visit: **http://localhost:8000/test-accounts**

This page provides a user-friendly interface to access all test accounts with detailed information about each account and available features.

### **2. Available Test Accounts**

#### **👤 John Doe (Regular Staff)**
- **Staff ID:** RCC-002
- **Email:** john.doe@africacdc.org
- **Position:** Public Health Officer
- **Department:** Disease Surveillance
- **Features:** Full staff dashboard access with sample attendance data

#### **👤 Jane Smith (Regular Staff)**
- **Staff ID:** RCC-003
- **Email:** jane.smith@africacdc.org
- **Position:** Epidemiologist
- **Department:** Capacity Building
- **Features:** Alternative staff account for testing data isolation

#### **👨‍💼 Sarah Johnson (Administrator)**
- **Staff ID:** RCC-004
- **Email:** sarah.johnson@africacdc.org
- **Position:** Regional Coordinator
- **Department:** Administration
- **Features:** Full admin access (when admin features are implemented)

### **3. Direct Login Links**
- **John Doe:** http://localhost:8000/test-login/RCC-002
- **Jane Smith:** http://localhost:8000/test-login/RCC-003
- **Sarah Johnson:** http://localhost:8000/test-login/RCC-004

## 🔧 **Testing Features**

### **✅ Staff Dashboard**
- Real-time attendance overview
- Monthly statistics and summaries
- Quick action buttons
- Professional Africa CDC branding
- Mobile-responsive design

### **📍 GPS Attendance System**
- **Clock In/Out:** Real-time GPS location tracking
- **OpenStreetMap Integration:** Interactive maps
- **Address Resolution:** Automatic address lookup
- **Location Validation:** Ensures GPS data before operations
- **Live Updates:** Real-time clock and location display

### **📊 Attendance History**
- **Comprehensive Records:** Detailed attendance history
- **Filtering:** Month-based filtering with date picker
- **Statistics:** Total days, hours, averages
- **Export:** CSV download functionality
- **Interactive Maps:** View attendance locations
- **Pagination:** Efficient handling of large datasets

### **👤 Profile Management**
- **Personal Information:** Update name, phone, etc.
- **Photo Upload:** Profile picture with preview
- **Employment Info:** View position, department, etc.
- **Activity Timeline:** Recent attendance activity
- **Statistics:** Personal attendance metrics

## 📱 **Testing Checklist**

### **Basic Functionality**
- [ ] Access test accounts page
- [ ] Login with different test accounts
- [ ] Navigate through dashboard sections
- [ ] View attendance statistics

### **GPS & Location Features**
- [ ] Allow location access when prompted
- [ ] Test clock in functionality
- [ ] Test clock out functionality
- [ ] View location on interactive map
- [ ] Check address resolution

### **Data Management**
- [ ] Filter attendance history by month
- [ ] Export attendance data to CSV
- [ ] View attendance details in modal
- [ ] Check location maps in history

### **Profile Features**
- [ ] Update personal information
- [ ] Upload profile picture
- [ ] View employment information
- [ ] Check recent activity timeline

### **Mobile Responsiveness**
- [ ] Test on mobile devices
- [ ] Check navigation on small screens
- [ ] Verify touch interactions
- [ ] Confirm map functionality on mobile

## 🗄️ **Sample Data Included**

Each test staff account includes:
- **Past Week Attendance:** 5 days of sample attendance records
- **Realistic Times:** Random clock in/out times during business hours
- **GPS Coordinates:** Sample locations around Accra, Ghana
- **Complete Records:** Full attendance data with hours calculation

## 🛠️ **Technical Notes**

### **Authentication Bypass**
The test login system bypasses Microsoft SSO for development purposes. In production:
- Remove the `/test-accounts` and `/test-login` routes
- Use proper Microsoft SSO authentication
- Remove the test accounts view file

### **Database Seeding**
If you need to recreate the test data:
```bash
php artisan db:seed --class=TestStaffSeeder
```

### **GPS Requirements**
- **HTTPS:** GPS features require HTTPS in production
- **Browser Permissions:** Users must allow location access
- **Fallback:** System defaults to Accra, Ghana if location unavailable

## 🌍 **Location Settings**

The system is configured for West Africa:
- **Default Location:** Accra, Ghana (5.6037, -0.1870)
- **Map Provider:** OpenStreetMap (free, no API key required)
- **Address Service:** Nominatim reverse geocoding
- **Timezone:** Africa/Accra (configurable)

## 🔒 **Security Features**

Even in test mode, the system maintains:
- **CSRF Protection:** All forms protected
- **Input Validation:** Sanitized user inputs
- **File Upload Security:** Image validation and size limits
- **Authentication Guards:** Proper session management
- **Route Protection:** Middleware-based access control

## 📋 **Next Steps**

After testing the staff dashboard system, you can proceed with:
1. **Option B:** Complete Admin Staff Management
2. **Option C:** Website Content Management
3. **Option D:** Microsoft SSO Setup
4. **Additional Features:** Weekly Tracker, Missions, Leave Management

## 🚨 **Important Reminders**

1. **Development Only:** Test accounts are for development/testing only
2. **Remove in Production:** Delete test routes and accounts before deployment
3. **Location Privacy:** GPS data is only used for attendance tracking
4. **Browser Compatibility:** Tested on modern browsers with GPS support
5. **Data Backup:** Sample data can be regenerated with the seeder

---

**Happy Testing!** 🎉

The Staff Dashboard & Attendance System is fully functional and ready for comprehensive testing. 

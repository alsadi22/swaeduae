import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

const resources = {
  en: {
    translation: {
      welcome: "Welcome",
      home: "Home",
      login: "Login",
      logout: "Logout",
      register: "Register",
      dashboard: "Dashboard",
      profile: "Profile",
      events: "Events",
      certificates: "Certificates",
      admin: "Admin",
      volunteer_hours: "Volunteer Hours",
      loading: "Loading...",
      kyc_upload: "Upload KYC Document",
      submit: "Submit",
      event_registration: "Event Registration",
      certificate_download: "Download Certificate",
      language: "Language",
      english: "English",
      arabic: "Arabic",
      admin_dashboard: "Admin Dashboard",
      manage_users: "Manage Users",
      manage_events: "Manage Events",
      name: "Name",
      email: "Email",
      password: "Password",
      error: "Error",
      success: "Success",
      submit_success: "Submitted successfully",
      submit_error: "Submission failed",
      no_events: "No events found",
      no_certificates: "No certificates found"
    }
  },
  ar: {
    translation: {
      welcome: "مرحبا",
      home: "الرئيسية",
      login: "تسجيل الدخول",
      logout: "تسجيل خروج",
      register: "إنشاء حساب",
      dashboard: "لوحة التحكم",
      profile: "الملف الشخصي",
      events: "الفعاليات",
      certificates: "الشهادات",
      admin: "الإدارة",
      volunteer_hours: "ساعات التطوع",
      loading: "جاري التحميل...",
      kyc_upload: "تحميل مستند إثبات الهوية",
      submit: "إرسال",
      event_registration: "تسجيل الفعالية",
      certificate_download: "تحميل الشهادة",
      language: "اللغة",
      english: "الإنجليزية",
      arabic: "العربية",
      admin_dashboard: "لوحة الإدارة",
      manage_users: "إدارة المستخدمين",
      manage_events: "إدارة الفعاليات",
      name: "الاسم",
      email: "البريد الإلكتروني",
      password: "كلمة المرور",
      error: "خطأ",
      success: "نجاح",
      submit_success: "تم الإرسال بنجاح",
      submit_error: "فشل الإرسال",
      no_events: "لا توجد فعاليات",
      no_certificates: "لا توجد شهادات"
    }
  }
};

i18n.use(initReactI18next).init({
  resources,
  lng: 'en',
  fallbackLng: 'en',
  interpolation: { escapeValue: false }
});

export default i18n;

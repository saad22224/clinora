<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\RadiologyExam;
use Illuminate\Database\Seeder;

class MedicalCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $labs = [
            ['CBC', 'Complete Blood Count', 'صورة دم كاملة', 'hematology'],
            ['ESR', 'Erythrocyte Sedimentation Rate', 'سرعة الترسيب', 'hematology'],
            ['CRP', 'C-Reactive Protein', 'بروتين سي التفاعلي', 'chemistry'],
            ['FBS', 'Fasting Blood Sugar', 'سكر صائم', 'chemistry'],
            ['RBS', 'Random Blood Sugar', 'سكر عشوائي', 'chemistry'],
            ['HbA1c', 'Glycated Hemoglobin', 'السكر التراكمي', 'chemistry'],
            ['Lipid', 'Lipid Profile', 'دهون الدم', 'chemistry'],
            ['LFT', 'Liver Function Tests', 'وظائف كبد', 'chemistry'],
            ['KFT', 'Kidney Function Tests', 'وظائف كلى', 'chemistry'],
            ['Urea', 'Blood Urea', 'يوريا', 'chemistry'],
            ['Creat', 'Serum Creatinine', 'كرياتينين', 'chemistry'],
            ['UA', 'Uric Acid', 'حمض اليوريك', 'chemistry'],
            ['TSH', 'Thyroid Stimulating Hormone', 'الهرمون المحفز للغدة الدرقية', 'hormones'],
            ['FT4', 'Free T4', 'ثيروكسين حر', 'hormones'],
            ['FT3', 'Free T3', 'ثلاثي يودوثيرونين حر', 'hormones'],
            ['VitD', 'Vitamin D 25-OH', 'فيتامين د', 'vitamins'],
            ['B12', 'Vitamin B12', 'فيتامين ب12', 'vitamins'],
            ['Ferritin', 'Serum Ferritin', 'فيريتين', 'hematology'],
            ['Iron', 'Serum Iron', 'حديد الدم', 'hematology'],
            ['Urine', 'Complete Urine Analysis', 'تحليل بول كامل', 'urine'],
            ['Stool', 'Stool Analysis', 'تحليل براز', 'stool'],
            ['PT', 'Prothrombin Time', 'زمن البروثرومبين', 'coagulation'],
            ['INR', 'International Normalized Ratio', 'INR', 'coagulation'],
            ['ABG', 'Arterial Blood Gases', 'غازات الدم الشرياني', 'critical'],
            ['Troponin', 'Troponin I/T', 'تروبونين', 'cardiac'],
            ['CKMB', 'CK-MB', 'إنزيم القلب CK-MB', 'cardiac'],
            ['HBsAg', 'Hepatitis B Surface Antigen', 'فيروس الكبد ب', 'serology'],
            ['HCV', 'Hepatitis C Antibody', 'فيروس الكبد ج', 'serology'],
            ['HIV', 'HIV Antibody', 'فيروس نقص المناعة', 'serology'],
            ['Pregnancy', 'Beta-HCG Blood', 'حمل في الدم', 'hormones'],
        ];

        foreach ($labs as [$code, $en, $ar, $category]) {
            LabTest::query()->updateOrCreate(
                ['code' => $code],
                ['name_en' => $en, 'name_ar' => $ar, 'category' => $category, 'is_active' => true]
            );
        }

        $radiology = [
            ['CXR', 'Chest X-Ray', 'أشعة على الصدر', 'xray'],
            ['AXR', 'Abdominal X-Ray', 'أشعة على البطن', 'xray'],
            ['SkullXR', 'Skull X-Ray', 'أشعة على الجمجمة', 'xray'],
            ['SpineXR', 'Spine X-Ray', 'أشعة على العمود الفقري', 'xray'],
            ['KUB', 'KUB X-Ray', 'أشعة على المسالك البولية', 'xray'],
            ['USAbd', 'Abdominal Ultrasound', 'موجات فوق صوتية على البطن', 'ultrasound'],
            ['USPelvis', 'Pelvic Ultrasound', 'موجات فوق صوتية على الحوض', 'ultrasound'],
            ['USThyroid', 'Thyroid Ultrasound', 'موجات فوق صوتية على الغدة الدرقية', 'ultrasound'],
            ['USBreast', 'Breast Ultrasound', 'موجات فوق صوتية على الثدي', 'ultrasound'],
            ['USObstetric', 'Obstetric Ultrasound', 'موجات فوق صوتية حمل', 'ultrasound'],
            ['CTBrain', 'CT Brain', 'أشعة مقطعية على المخ', 'ct'],
            ['CTChest', 'CT Chest', 'أشعة مقطعية على الصدر', 'ct'],
            ['CTAbd', 'CT Abdomen/Pelvis', 'أشعة مقطعية على البطن والحوض', 'ct'],
            ['MRIBrain', 'MRI Brain', 'رنين مغناطيسي على المخ', 'mri'],
            ['MRISpine', 'MRI Spine', 'رنين مغناطيسي على العمود الفقري', 'mri'],
            ['MRIKnee', 'MRI Knee', 'رنين مغناطيسي على الركبة', 'mri'],
            ['Echo', 'Echocardiography', 'موجات صوتية على القلب', 'cardiac'],
            ['ECG', 'Electrocardiogram', 'رسم قلب كهربائي', 'cardiac'],
            ['Mammo', 'Mammography', 'تصوير الثدي الشعاعي', 'mammography'],
            ['DEXA', 'Bone Densitometry (DEXA)', 'قياس كثافة العظام', 'other'],
            ['Doppler', 'Arterial/Venous Doppler', 'دوبلر شرايين/أوردة', 'ultrasound'],
        ];

        foreach ($radiology as [$code, $en, $ar, $category]) {
            RadiologyExam::query()->updateOrCreate(
                ['code' => $code],
                ['name_en' => $en, 'name_ar' => $ar, 'category' => $category, 'is_active' => true]
            );
        }
    }
}

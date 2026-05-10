--CREATE DATABASE HospDB;

--USE HospDB;

-- WARD
CREATE TABLE WARD (
    WardName    VARCHAR(50)  NOT NULL,
    Specialty   VARCHAR(100) NOT NULL,
    CONSTRAINT PK_WARD PRIMARY KEY (WardName)
);

-- DOCTOR (self-referencing FK for consultant hierarchy)
CREATE TABLE DOCTOR (
    StaffNo       INT          NOT NULL IDENTITY(1,1),
    ConsultantID  INT          NULL,
    Name          VARCHAR(100) NOT NULL,
    Position      VARCHAR(50)  NOT NULL,
    Specialty     VARCHAR(100) NOT NULL,
    CONSTRAINT PK_DOCTOR PRIMARY KEY (StaffNo),
    CONSTRAINT FK_DOCTOR_CONSULTANT FOREIGN KEY (ConsultantID)
        REFERENCES DOCTOR(StaffNo)
);

-- NURSE
CREATE TABLE NURSE (
    NurseID    INT          NOT NULL IDENTITY(1,1),
    WardName   VARCHAR(50)  NOT NULL,
    NurseName  VARCHAR(100) NOT NULL,
    Role       VARCHAR(50)  NOT NULL,
    CONSTRAINT PK_NURSE PRIMARY KEY (NurseID),
    CONSTRAINT FK_NURSE_WARD FOREIGN KEY (WardName)
        REFERENCES WARD(WardName)
);

-- CARE_UNIT (composite PK)
CREATE TABLE CARE_UNIT (
    UnitNumber       INT         NOT NULL,
    WardName         VARCHAR(50) NOT NULL,
    InchargeNurseID  INT         NOT NULL,
    CONSTRAINT PK_CARE_UNIT PRIMARY KEY (UnitNumber, WardName),
    CONSTRAINT FK_CU_WARD  FOREIGN KEY (WardName) REFERENCES WARD(WardName),
    CONSTRAINT FK_CU_NURSE FOREIGN KEY (InchargeNurseID) REFERENCES NURSE(NurseID)
);

-- PATIENT
CREATE TABLE PATIENT (
    PatientNo       INT          NOT NULL IDENTITY(1,1),
    WardName        VARCHAR(50)  NOT NULL,
    UnitNumber      INT          NOT NULL,
    PrimaryDoctorID INT          NOT NULL,
    PatientName     VARCHAR(100) NOT NULL,
    DOB             DATE         NOT NULL,
    BedNo           INT          NOT NULL,
    DateAdmitted    DATE         NOT NULL,
    CONSTRAINT PK_PATIENT PRIMARY KEY (PatientNo),
    CONSTRAINT FK_PAT_WARD   FOREIGN KEY (WardName) REFERENCES WARD(WardName),
    CONSTRAINT FK_PAT_UNIT   FOREIGN KEY (UnitNumber, WardName)
                             REFERENCES CARE_UNIT(UnitNumber, WardName),
    CONSTRAINT FK_PAT_DOCTOR FOREIGN KEY (PrimaryDoctorID) REFERENCES DOCTOR(StaffNo)
);

-- MEDICAL_HISTORY (composite PK)
CREATE TABLE MEDICAL_HISTORY (
    PatientNo      INT          NOT NULL,
    StaffNo        INT          NOT NULL,
    ComplaintCode  VARCHAR(20)  NOT NULL,
    DateStarted    DATE         NOT NULL,
    TreatmentCode  VARCHAR(20)  NULL,
    DateEnded      DATE         NULL,
    CONSTRAINT PK_MEDICAL_HISTORY
        PRIMARY KEY (PatientNo, StaffNo, ComplaintCode, DateStarted),
    CONSTRAINT FK_MH_PATIENT FOREIGN KEY (PatientNo) REFERENCES PATIENT(PatientNo),
    CONSTRAINT FK_MH_DOCTOR  FOREIGN KEY (StaffNo)   REFERENCES DOCTOR(StaffNo)
);

-- INSERTION

-- WARD (10 records)
INSERT INTO WARD (WardName, Specialty) VALUES
('Cardiology',      'Cardiovascular Disease'),
('Neurology',       'Brain and Nervous System'),
('Orthopedics',     'Bone and Joint Care'),
('Pediatrics',      'Child Healthcare'),
('Oncology',        'Cancer Treatment'),
('Gynecology',      'Women Health'),
('Dermatology',     'Skin Disorders'),
('Pulmonology',     'Respiratory Diseases'),
('Gastroenterology','Digestive System'),
('Nephrology',      'Kidney Disease');

-- DOCTOR (10 records; ConsultantID set after insert via UPDATE)
INSERT INTO DOCTOR (ConsultantID, Name, Position, Specialty) VALUES
(NULL, 'Dr. Ayesha Malik',    'Consultant',      'Cardiology'),
(NULL, 'Dr. Hassan Raza',     'Consultant',      'Neurology'),
(NULL, 'Dr. Sana Tariq',      'Consultant',      'Orthopedics'),
(NULL, 'Dr. Bilal Ahmed',     'Consultant',      'Pediatrics'),
(NULL, 'Dr. Nadia Iqbal',     'Consultant',      'Oncology'),
(NULL, 'Dr. Usman Khan',      'Senior Registrar','Cardiology'),
(NULL, 'Dr. Fatima Zahra',    'Senior Registrar','Neurology'),
(NULL, 'Dr. Kamran Yousuf',   'Registrar',       'Orthopedics'),
(NULL, 'Dr. Rabia Siddiqui',  'Registrar',       'Pediatrics'),
(NULL, 'Dr. Zain ul Abideen', 'Registrar',       'Oncology');

-- Registrars supervised by their department consultants
UPDATE DOCTOR SET ConsultantID = 1 WHERE StaffNo = 6;
UPDATE DOCTOR SET ConsultantID = 2 WHERE StaffNo = 7;
UPDATE DOCTOR SET ConsultantID = 3 WHERE StaffNo = 8;
UPDATE DOCTOR SET ConsultantID = 4 WHERE StaffNo = 9;
UPDATE DOCTOR SET ConsultantID = 5 WHERE StaffNo = 10;

-- NURSE (15 records)
INSERT INTO NURSE (WardName, NurseName, Role) VALUES
('Cardiology',      'Nurse Amna Bashir',       'Head Nurse'),
('Cardiology',      'Nurse Sadia Perveen',     'Staff Nurse'),
('Neurology',       'Nurse Hira Nawaz',        'Head Nurse'),
('Neurology',       'Nurse Zara Hussain',      'Staff Nurse'),
('Orthopedics',     'Nurse Maria Qureshi',     'Head Nurse'),
('Orthopedics',     'Nurse Saima Akhtar',      'Staff Nurse'),
('Pediatrics',      'Nurse Noor Fatima',       'Head Nurse'),
('Pediatrics',      'Nurse Iqra Shahid',       'Staff Nurse'),
('Oncology',        'Nurse Gulshan Bibi',      'Head Nurse'),
('Oncology',        'Nurse Rubab Zaidi',       'Staff Nurse'),
('Gynecology',      'Nurse Tahira Malik',      'Head Nurse'),
('Pulmonology',     'Nurse Asma Javed',        'Head Nurse'),
('Gastroenterology','Nurse Raheela Baig',      'Head Nurse'),
('Nephrology',      'Nurse Shabana Riaz',      'Head Nurse'),
('Dermatology',     'Nurse Kiran Saleem',      'Head Nurse');

-- CARE_UNIT (15 records)
INSERT INTO CARE_UNIT (UnitNumber, WardName, InchargeNurseID) VALUES
(101, 'Cardiology',       1),
(102, 'Cardiology',       2),
(201, 'Neurology',        3),
(202, 'Neurology',        4),
(301, 'Orthopedics',      5),
(302, 'Orthopedics',      6),
(401, 'Pediatrics',       7),
(402, 'Pediatrics',       8),
(501, 'Oncology',         9),
(502, 'Oncology',        10),
(601, 'Gynecology',      11),
(701, 'Pulmonology',     12),
(801, 'Gastroenterology',13),
(901, 'Nephrology',      14),
(1001,'Dermatology',     15);

-- PATIENT (30 records)
INSERT INTO PATIENT (WardName, UnitNumber, PrimaryDoctorID, PatientName, DOB, BedNo, DateAdmitted) VALUES
('Cardiology',       101, 1,  'Ali Hassan',           '1978-03-12', 1,  '2025-01-05'),
('Cardiology',       101, 6,  'Sara Baig',            '1990-07-22', 2,  '2025-01-10'),
('Cardiology',       102, 1,  'Tariq Mahmood Saab',   '1965-11-30', 3,  '2025-01-15'),
('Neurology',        201, 2,  'Amna Riaz',            '1982-05-18', 1,  '2025-01-08'),
('Neurology',        201, 7,  'Faisal Butt',          '1975-09-04', 2,  '2025-01-12'),
('Neurology',        202, 2,  'Khadija Noor',         '1993-12-25', 3,  '2025-01-20'),
('Orthopedics',      301, 3,  'Umar Farooq',          '1985-06-14', 1,  '2025-02-01'),
('Orthopedics',      301, 8,  'Zainab Khalid',        '2001-02-28', 2,  '2025-02-05'),
('Orthopedics',      302, 3,  'Ahmed Nawaz',          '1958-08-19', 3,  '2025-02-10'),
('Pediatrics',       401, 4,  'Baby Hamza Siddiqui',  '2022-04-10', 1,  '2025-02-03'),
('Pediatrics',       401, 9,  'Baby Fatima Aziz',     '2021-11-15', 2,  '2025-02-07'),
('Pediatrics',       402, 4,  'Baby Yusuf Malik',     '2020-07-22', 3,  '2025-02-12'),
('Oncology',         501, 5,  'Mehwish Rana',         '1972-10-05', 1,  '2025-02-15'),
('Oncology',         501, 10, 'Junaid Akhtar',        '1968-03-30', 2,  '2025-02-18'),
('Oncology',         502, 5,  'Hina Saleem',          '1980-01-14', 3,  '2025-02-20'),
('Gynecology',       601, 5,  'Rukhsana Bibi',        '1988-09-09', 1,  '2025-03-01'),
('Gynecology',       601, 5,  'Nasreen Perveen',      '1995-12-03', 2,  '2025-03-04'),
('Pulmonology',      701, 1,  'Shahzad Iqbal',        '1960-04-17', 1,  '2025-03-06'),
('Pulmonology',      701, 6,  'Rashida Khatoon',      '1970-08-22', 2,  '2025-03-09'),
('Gastroenterology', 801, 2,  'Imran Chaudhry',       '1983-05-31', 1,  '2025-03-10'),
('Gastroenterology', 801, 7,  'Samina Waheed',        '1976-02-14', 2,  '2025-03-14'),
('Nephrology',       901, 3,  'Pervez Masih',         '1955-07-08', 1,  '2025-03-16'),
('Nephrology',       901, 8,  'Lubna Aslam',          '1987-11-27', 2,  '2025-03-19'),
('Dermatology',     1001, 4,  'Asad Zaman',           '1998-03-21', 1,  '2025-03-21'),
('Dermatology',     1001, 9,  'Bushra Naz',           '2000-06-18', 2,  '2025-03-23'),
('Cardiology',       102, 6,  'Naeem Ullah',          '1963-10-11', 4,  '2025-03-25'),
('Neurology',        202, 7,  'Sadaf Jabeen',         '1991-08-07', 4,  '2025-03-27'),
('Orthopedics',      302, 8,  'Waqas Mehmood',        '1979-01-16', 4,  '2025-03-28'),
('Pediatrics',       402, 9,  'Baby Aiza Rehman',     '2023-02-05', 4,  '2025-03-30'),
('Oncology',         502, 10, 'Ghulam Mustafa',       '1952-12-01', 4,  '2025-04-01');

-- MEDICAL_HISTORY (20 records)
INSERT INTO MEDICAL_HISTORY (PatientNo, StaffNo, ComplaintCode, DateStarted, TreatmentCode, DateEnded) VALUES
(1,  1,  'C001', '2025-01-05', 'T101', '2025-01-20'),
(2,  6,  'C002', '2025-01-10', 'T102', NULL),
(3,  1,  'C003', '2025-01-15', 'T103', '2025-02-01'),
(4,  2,  'N001', '2025-01-08', 'T201', '2025-01-25'),
(5,  7,  'N002', '2025-01-12', 'T202', NULL),
(6,  2,  'N003', '2025-01-20', 'T203', '2025-02-10'),
(7,  3,  'O001', '2025-02-01', 'T301', '2025-02-20'),
(8,  8,  'O002', '2025-02-05', 'T302', NULL),
(9,  3,  'O003', '2025-02-10', 'T303', '2025-03-01'),
(10, 4,  'P001', '2025-02-03', 'T401', '2025-02-18'),
(11, 9,  'P002', '2025-02-07', 'T402', NULL),
(12, 4,  'P003', '2025-02-12', 'T403', '2025-03-05'),
(13, 5,  'K001', '2025-02-15', 'T501', NULL),
(14, 10, 'K002', '2025-02-18', 'T502', NULL),
(15, 5,  'K003', '2025-02-20', 'T503', '2025-03-15'),
(16, 5,  'G001', '2025-03-01', 'T601', '2025-03-12'),
(18, 1,  'C004', '2025-03-06', 'T104', NULL),
(20, 2,  'N004', '2025-03-10', 'T204', NULL),
(22, 3,  'O004', '2025-03-16', 'T304', NULL),
(30, 10, 'K004', '2025-04-01', 'T504', NULL);

-- 1
SELECT 
    c.Name AS ConsultantName, 
    c.Specialty,
    d.Name AS TeamDoctorName, 
    d.Position AS TeamDoctorPosition
FROM DOCTOR c
JOIN DOCTOR d ON c.StaffNo = d.ConsultantID
WHERE c.Position = 'Consultant'
ORDER BY c.Name, d.Name;

-- 2
SELECT 
    w.WardName, 
    cu.UnitNumber AS CareUnit, 
    n.NurseName AS InchargeNurse, 
    n.Role AS NurseRole
FROM WARD w
JOIN CARE_UNIT cu ON w.WardName = cu.WardName
JOIN NURSE n ON cu.InchargeNurseID = n.NurseID
ORDER BY w.WardName, cu.UnitNumber;

-- 3
SELECT 
    p.PatientName, 
    mh.ComplaintCode, 
    mh.TreatmentCode, 
    mh.DateStarted, 
    mh.DateEnded
FROM PATIENT p
JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo
ORDER BY p.PatientName, mh.DateStarted;

-- 4
SELECT 
    d.Name AS DoctorName, 
    p.PatientName, 
    n.NurseName AS StaffNurse
FROM DOCTOR d
JOIN PATIENT p ON d.StaffNo = p.PrimaryDoctorID
JOIN CARE_UNIT cu ON p.UnitNumber = cu.UnitNumber AND p.WardName = cu.WardName
JOIN NURSE n ON cu.InchargeNurseID = n.NurseID
WHERE d.Position = 'Junior Houseman'
ORDER BY d.Name, p.PatientName;

-- 5
SELECT 
    Name AS ConsultantName, 
    Specialty
FROM DOCTOR
WHERE Position = 'Consultant' AND Specialty IN (
    SELECT Specialty
    FROM DOCTOR
    WHERE Position = 'Consultant'
    GROUP BY Specialty
    HAVING COUNT(*) = 1
);

-- 6
SELECT 
    mh.ComplaintCode, 
    mh.TreatmentCode, 
    d.Name AS DoctorName, 
    d.Position, 
    d.Specialty
FROM MEDICAL_HISTORY mh
JOIN DOCTOR d ON mh.StaffNo = d.StaffNo
ORDER BY mh.ComplaintCode, mh.TreatmentCode;

-- 7
SELECT 
    p.PatientName, 
    mh.ComplaintCode, 
    mh.TreatmentCode
FROM PATIENT p
JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo
WHERE p.PatientNo IN (
    SELECT PatientNo
    FROM MEDICAL_HISTORY
    GROUP BY PatientNo
    HAVING COUNT(DISTINCT ComplaintCode) > 1
)
ORDER BY p.PatientName, mh.ComplaintCode;

-- 8
SELECT 
    mh.ComplaintCode, 
    mh.TreatmentCode, 
    p.PatientName
FROM MEDICAL_HISTORY mh
JOIN PATIENT p ON mh.PatientNo = p.PatientNo
ORDER BY mh.ComplaintCode, mh.TreatmentCode, p.PatientName;

-- 9
SELECT 
    StaffNo,
    Name AS DoctorName, 
    Position,
    Specialty
FROM DOCTOR 
WHERE StaffNo = 6; 

-- 10
SELECT 
    p.PatientName, 
    p.DOB, 
    p.DateAdmitted, 
    w.WardName, 
    cu.UnitNumber, 
    d.Name AS PrimaryDoctor, 
    mh.ComplaintCode, 
    mh.DateStarted, 
    mh.TreatmentCode, 
    mh.DateEnded
FROM PATIENT p
JOIN WARD w ON p.WardName = w.WardName
JOIN CARE_UNIT cu ON p.UnitNumber = cu.UnitNumber AND p.WardName = cu.WardName
JOIN DOCTOR d ON p.PrimaryDoctorID = d.StaffNo
LEFT JOIN MEDICAL_HISTORY mh ON p.PatientNo = mh.PatientNo
WHERE p.PatientNo = 1; 

-- 11
SELECT 
    TreatmentCode, 
    DateStarted, 
    DateEnded,
    ComplaintCode
FROM MEDICAL_HISTORY
WHERE ComplaintCode = 'C001' 
  AND DateStarted >= '2025-01-01' 
  AND DateStarted <= '2025-12-31'
ORDER BY TreatmentCode;

-- 12
SELECT 
    PositionOrRole, 
    COUNT(*) AS StaffCount
FROM (
    SELECT Position AS PositionOrRole FROM DOCTOR
    UNION ALL
    SELECT Role AS PositionOrRole FROM NURSE
) AS AllStaff
GROUP BY PositionOrRole
ORDER BY StaffCount DESC;
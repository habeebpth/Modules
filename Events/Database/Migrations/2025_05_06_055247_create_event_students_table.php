<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->unique();
            $table->string('student_name');
            $table->enum('gender', ['male', 'female']);
            $table->timestamps();
        });

        $students = [
            // Boys
            ['student_id' => 43, 'student_name' => 'Hamad Ali Sultan Khamis M.BinKhadim Suwaidi', 'gender' => 'male'],
            ['student_id' => 326, 'student_name' => 'Sultan Ahmed Mohammed Obaid Alghumlasi', 'gender' => 'male'],
            ['student_id' => 346, 'student_name' => 'Sheikh Eissa Abdulaziz Eissa Abdulaziz Almualla', 'gender' => 'male'],
            ['student_id' => 518, 'student_name' => 'OBAID ALI OBAID BUSANNAD ALSHAMSI', 'gender' => 'male'],
            ['student_id' => 610, 'student_name' => 'Abdalla Mohamed Abdalla Allay Alnaqbi', 'gender' => 'male'],
            ['student_id' => 1075, 'student_name' => 'Khaled Mohammad Suleiman Alsyoof', 'gender' => 'male'],
            ['student_id' => 1164, 'student_name' => 'Mohamed Amir Amin Abdelaziz Buyaraba', 'gender' => 'male'],
            ['student_id' => 1183, 'student_name' => 'Sheikh Humaid Sheikh Mualla Humaid Ahmed Rashid Almualla', 'gender' => 'male'],
            ['student_id' => 1296, 'student_name' => 'Saud Abdelaziz Mohamed Ahmed Alyassi', 'gender' => 'male'],
            ['student_id' => 1400, 'student_name' => 'Ibrahim Alami', 'gender' => 'male'],
            ['student_id' => 1488, 'student_name' => 'MHD ANAS MOHAMAD KHOJA', 'gender' => 'male'],
            ['student_id' => 1560, 'student_name' => 'Omar Marwan Audeh', 'gender' => 'male'],
            ['student_id' => 1610, 'student_name' => 'Hamza Saeed Habashy Aly', 'gender' => 'male'],
            ['student_id' => 1714, 'student_name' => 'Meranai Khanzali', 'gender' => 'male'],
            ['student_id' => 1764, 'student_name' => 'Mubeen Noor Hossain', 'gender' => 'male'],
            ['student_id' => 1971, 'student_name' => 'Ibrahim Wael Ahmed Mohamed Amin Noureldin', 'gender' => 'male'],
            ['student_id' => 2068, 'student_name' => 'Ward Mahmoud Charbouch', 'gender' => 'male'],
            ['student_id' => 2072, 'student_name' => 'Osama Amjad Hamdan', 'gender' => 'male'],
            ['student_id' => 2300, 'student_name' => 'Racim Harkat', 'gender' => 'male'],
            ['student_id' => 2374, 'student_name' => 'Majed Omar Alnemer', 'gender' => 'male'],
            ['student_id' => 2429, 'student_name' => 'Yousuf Atef Abdelbar Ahmed', 'gender' => 'male'],
            ['student_id' => 2431, 'student_name' => 'Ahmad Muhammad Saad Muhammad Saad Nisar', 'gender' => 'male'],
            ['student_id' => 2449, 'student_name' => 'Rayyan Faizi Hameed', 'gender' => 'male'],
            ['student_id' => 2460, 'student_name' => 'Hamza Jawwad jawwad Manzoor', 'gender' => 'male'],
            ['student_id' => 2464, 'student_name' => 'Uzair Varsaji', 'gender' => 'male'],
            ['student_id' => 2467, 'student_name' => 'Mohammed Wajeeh Sadiq', 'gender' => 'male'],
            ['student_id' => 2605, 'student_name' => 'Aboubakr Mahmoud Soliman', 'gender' => 'male'],
            ['student_id' => 2737, 'student_name' => 'Mayed Nasser Rahmah Alasam', 'gender' => 'male'],
            ['student_id' => 2794, 'student_name' => 'Ayaan Khan Faisal Mukhtar Khan', 'gender' => 'male'],
            ['student_id' => 2815, 'student_name' => 'Yousif Firas Hameed Hasan Al Obaidi', 'gender' => 'male'],
            ['student_id' => 2819, 'student_name' => 'Salim Ahmed Nour', 'gender' => 'male'],
            ['student_id' => 2900, 'student_name' => 'Ziad Ali', 'gender' => 'male'],

            // Girls
            ['student_id' => 205, 'student_name' => 'Shaimaa Mohamad AbdulKader', 'gender' => 'female'],
            ['student_id' => 1371, 'student_name' => 'Adeeba Alam', 'gender' => 'female'],
            ['student_id' => 1392, 'student_name' => 'Leen Mohamad Akkad', 'gender' => 'female'],
            ['student_id' => 1588, 'student_name' => 'Lily Maged Mahmoud Samy Abedalaaty Saleh Elsharkawy', 'gender' => 'female'],
            ['student_id' => 1637, 'student_name' => 'Lilian Mohamed Asaad', 'gender' => 'female'],
            ['student_id' => 1657, 'student_name' => 'Alesar Sinmar Al Sayed', 'gender' => 'female'],
            ['student_id' => 1703, 'student_name' => 'Paashma Qamar Qamar UD Din', 'gender' => 'female'],
            ['student_id' => 1720, 'student_name' => 'Natalie George Alhames', 'gender' => 'female'],
            ['student_id' => 1729, 'student_name' => 'Meera Mourad', 'gender' => 'female'],
            ['student_id' => 1863, 'student_name' => 'Naba Alvi', 'gender' => 'female'],
            ['student_id' => 1995, 'student_name' => 'Mina Ghulam Farooq Ghulam Farooq', 'gender' => 'female'],
            ['student_id' => 2005, 'student_name' => 'Rania Fayes Khalil Amro', 'gender' => 'female'],
            ['student_id' => 2010, 'student_name' => 'Elesha Elie Zakhia', 'gender' => 'female'],
            ['student_id' => 2029, 'student_name' => 'Sarah Mohammed Ali Abbas Altaei', 'gender' => 'female'],
            ['student_id' => 2030, 'student_name' => 'Ingy Ashraf Kraidy', 'gender' => 'female'],
            ['student_id' => 2185, 'student_name' => 'Rafeeah Abdelaziz Mhamed Humaid Alomrani Alshamsi', 'gender' => 'female'],
            ['student_id' => 2217, 'student_name' => 'Aseel Anas Sibaii', 'gender' => 'female'],
            ['student_id' => 2269, 'student_name' => 'Meral Waddah Alseid', 'gender' => 'female'],
            ['student_id' => 2281, 'student_name' => 'Zuha Rao Salman Rao', 'gender' => 'female'],
            ['student_id' => 2286, 'student_name' => 'Haneen Hafiz Abdelaziz Ahmed', 'gender' => 'female'],
            ['student_id' => 2313, 'student_name' => 'Syeda Aliza Fatima Syed Muhammad Arshad Hussain', 'gender' => 'female'],
            ['student_id' => 2401, 'student_name' => 'Naomi Sam', 'gender' => 'female'],
            ['student_id' => 2413, 'student_name' => 'Zinab Mohamad Aswad', 'gender' => 'female'],
            ['student_id' => 2427, 'student_name' => 'Sadeem Samawal Abdelrahman Shalabi', 'gender' => 'female'],
            ['student_id' => 2461, 'student_name' => 'Maya Chrieh', 'gender' => 'female'],
            ['student_id' => 2486, 'student_name' => 'Hala Mohamad Abdelrahman Elhanoun', 'gender' => 'female'],
            ['student_id' => 2499, 'student_name' => 'Rabea Ziani', 'gender' => 'female'],
            ['student_id' => 2521, 'student_name' => 'Alishba Amin', 'gender' => 'female'],
            ['student_id' => 2689, 'student_name' => 'Sara Moradi', 'gender' => 'female'],
            ['student_id' => 2744, 'student_name' => 'Hana Ahmed Saeed Ibrahim', 'gender' => 'female'],
            ['student_id' => 2769, 'student_name' => 'Ghazal Abdolreza Seddighi', 'gender' => 'female'],
            ['student_id' => 2773, 'student_name' => 'Fareda Sameh Abdelkader Mohamed Bakier', 'gender' => 'female'],
        ];

        DB::table('event_students')->insert($students);
    }

    public function down(): void
    {
        Schema::dropIfExists('event_students');
    }
};

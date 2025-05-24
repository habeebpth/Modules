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
    public function up()
    {
        Schema::create('panchayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Get district IDs to use in panchayat seeding
        $districts = DB::table('districts')->select('id', 'slug')->get()->keyBy('slug');

        // Thiruvananthapuram Panchayats
        $thiruvananthapuramPanchayats = [
            "Amboori", "Anchuthengu", "Andoorkonam", "Anicadu", "Aruvikkara", "Aryanadu",
            "Athiyanoor", "Attingal", "Azhoor", "Balaramapuram", "Chenkal", "Cherunniyoor",
            "Chirayinkeezhu", "Elakamon", "Kadinamkulam", "Kallara", "Kalliyoor", "Kanjiramkulam",
            "Karakulam", "Karavaram", "Karumkulam", "Kattakada", "Keezhattingal", "Kilimanoor",
            "Kollayil", "Kottukal", "Kudappanakunnu", "Kulathoor", "Kunnathukal", "Kuttichal",
            "Madavoor", "Malayinkeezhu", "Manickal", "Mangalapuram", "Maranalloor", "Mudakkal",
            "Nagaroor", "Nedumangad", "Nellanad", "Neyyattinkara", "Ottasekharamangalam",
            "Pallichal", "Pallickal", "Panavoor", "Pangodu", "Parassala", "Peringamala",
            "Perumkadavila", "Perumpazhuthoor", "Peyad", "Pothencode", "Poovachal", "Pullampara",
            "Pulimath", "Puthukurichy", "Thiruvananthapuram", "Varkala", "Vamanapuram",
            "Vellanad", "Venganoor", "Vembayam", "Venpakal", "Vilavoorkkal", "Vilappil"
        ];

        // Kollam Panchayats
        $kollamPanchayats = [
            "Alappad", "Alayamon", "Anchal", "Arinalloor", "Chathannoor", "Chavara",
            "Chadayamangalam", "Chirakkara", "Clappana", "Edamulakkal", "Elamadu", "Elampalloor",
            "Ezhukone", "Irindavoor", "Kadakkal", "Kallelibhagom", "Kalluvathukkal", "Kareepra",
            "Karunagappally", "Kottarakkara", "Kottamkara", "Kulakkada", "Kulasekharapuram",
            "Kundara", "Kunnathur", "Mayyanad", "Melila", "Mukhathala", "Mylom", "Mynagappally",
            "Nedumpana", "Neduvathoor", "Neendakara", "Nilamel", "Oachira", "Panayam",
            "Panmana", "Pattazhi", "Pavithreswaram", "Perayam", "Perinad", "Poothakkulam",
            "Poruvazhy", "Pooyappally", "Sasthamcotta", "Sooranad North", "Sooranad South",
            "Thekkumbhagam", "Thevalakkara", "Thrikkaruva", "Thrikkovilvattom", "Ummannoor",
            "Vettikkavala", "Vilakkudy", "Yeroor"
        ];

        // Pathanamthitta Panchayats
        $pathanamthittaPanchayats = [
            "Adoor", "Anicadu", "Angadical North", "Angadical South", "Aranmula", "Aruvappulam",
            "Cherukole", "Chittar", "Enadimangalam", "Erathu", "Ezhamkulam", "Kadampanad",
            "Kadapra", "Kalanjoor", "Kallooppara", "Keekozhoor", "Kochukulam", "Kodumon",
            "Konni", "Koipuram", "Kottangal", "Kottanad", "Kozhencherry", "Kulanada",
            "Kumbanad", "Kunnamthanam", "Mallapally", "Mallappuzhassery", "Mangaram",
            "Mekozhoor", "Mylapra", "Naranganam", "Nedumpuram", "Niranam", "Omalloor",
            "Padiyoor", "Pallickal", "Pampady", "Pandalam", "Pandalam-Thekkekara", "Parakkode",
            "Pathanamthitta", "Peringara", "Perinad", "Pullad", "Pramadom", "Ranni",
            "Ranni-Angadi", "Ranni-Pazhavangadi", "Seethathode", "Thannithode", "Thottappuzhassery",
            "Thiruvalla East", "Thiruvalla West", "Thumpamon", "Vallicode", "Vadasserikara",
            "Vechoochira"
        ];

        // Alappuzha Panchayats
        $alappuzhaPanchayats = [
            "Ala", "Ambalappuzha North", "Ambalappuzha South", "Arattupuzha", "Arookutty",
            "Aroor", "Bharanikavu", "Budhanoor", "Champakulam", "Chennithala", "Cheppad",
            "Cherthala", "Cherthala South", "Chingoli", "Chunakkara", "Devikulangara", "Edathua",
            "Ezhupunna", "Harippad", "Kadakkarappally", "Kainakary", "Kandalloor", "Kanjikuzhy",
            "Karthikappally", "Karuvatta", "Kavalam", "Kodamthuruthu", "Kumarapuram", "Kuruvattur",
            "Mannanchery", "Mannar", "Mararikkulam North", "Mararikkulam South", "Muhamma",
            "Muttar", "Neelamperoor", "Nooranad", "Pallippad", "Panavally", "Pandanad",
            "Pattanakkad", "Perumbalam", "Pulincunnu", "Puliyur", "Punnapra North", "Punnapra South",
            "Purakad", "Ramankary", "Thaikattussery", "Thakazhy", "Thanneermukkom", "Thazhakara",
            "Thiruvanvandoor", "Thrikkunnappuzha", "Thuravoor", "Vallikunnam", "Vayalar",
            "Veeyapuram", "Veliyanad"
        ];

        // Kottayam Panchayats
        $kottayamPanchayats = [
            "Ayarkunnam", "Aymanam", "Athirampuzha", "Arpookara", "Chempu", "Chirakkadavu",
            "Elikkulam", "Ettumanoor", "Kadanad", "Kadaplamattom", "Kaduthuruthy", "Kallara",
            "Kanakkari", "Kangazha", "Kanjirappally", "Karoor", "Karukachal", "Kidangoor",
            "Kooroppada", "Kottayam", "Kozhuvanal", "Kumarakom", "Kurichy", "Kurichi",
            "Madappally", "Manimala", "Marangattupilly", "Maravanthuruthu", "Meenachil",
            "Melukavu", "Monipally", "Moonnilavu", "Mulakkulam", "Mundakayam", "Mutholy",
            "Nedumkunnam", "Neendoor", "Njeezhoor", "Pala", "Pampady", "Panachikkad",
            "Poonjar", "Poonjar-Thekkekara", "Puthuppally", "Ramapuram", "Thalappalam",
            "Thalanadu", "Thalanad", "Thalayazham", "Thalayolaparambu", "Thidanadu",
            "Thiruvarp", "T.V. Puram", "Udayanapuram", "Uzhavoor", "Vaikom", "Vakathanam",
            "Vechoor", "Veliyannoor", "Vellavoor", "Vijayapuram"
        ];

        // Idukki Panchayats
        $idukkiPanchayats = [
            "Adimaly", "Alakode", "Arakkulam", "Ayyappancoil", "Azhutha", "Chinnakanal",
            "Devikulam", "Edamalakkudy", "Edavetty", "Elappara", "Erattayar", "Idukki",
            "Kamakshy", "Kanchiyar", "Kanjikuzhi", "Kanthalloor", "Karimannoor", "Karimkunnam",
            "Kattappana", "Kokkayar", "Kumily", "Konnathady", "Kudayathoor", "Kumaramangalam",
            "Manakkad", "Mankulam", "Marayoor", "Mariyapuram", "Nedumkandam", "Pallivasal",
            "Pampadumpara", "Peerumade", "Peruvanthanam", "Purappuzha", "Rajakkad", "Rajakumari",
            "Santhanpara", "Senapathy", "Udumbannoor", "Upputhara", "Vandanmedu", "Vandiperiyar",
            "Vathikudy", "Vattavada", "Vellathooval", "Velliyamattom"
        ];

        // Ernakulam Panchayats
        $ernakulamPanchayats = [
            "Aikkaranadu", "Alengad", "Amballoor", "Angamaly", "Arakuzha", "Avoly",
            "Ayavana", "Ayyampuzha", "Chendamangalam", "Chengamanad", "Cheranalloor",
            "Chittattukara", "Choornikkara", "Chottanikkara", "Cochin", "Edakkattuvayal",
            "Edathala", "Edavanakkad", "Elanji", "Elavoor", "Ernakulam", "Kadamakudy",
            "Kadungalloor", "Kalady", "Kalamassery", "Kalloorkad", "Kanjoor", "Karukutty",
            "Karumalloor", "Kavalangad", "Keerampara", "Kizhakkambalam", "Kochi", "Koothattukulam",
            "Koovappady", "Kothamangalam", "Kottappady", "Kottuvally", "Kumbalam", "Kumbalangi",
            "Kunnathunad", "Kunnukara", "Malayattoor-Neeleswaram", "Maneed", "Manjalloor",
            "Manjapra", "Maradu", "Mazhuvannoor", "Mookkannoor", "Mudakuzha", "Mulavukad",
            "Muvattupuzha", "Nayarambalam", "Nedumbassery", "Nellikuzhi", "Njarakkal",
            "Okkal", "Paingottur", "Paipra", "Pallarimangalam", "Pallippuram", "Pampakuda",
            "Parakadavu", "Parakkadave", "Perumbavoor", "Pindimana", "Poothrikka", "Pothanikkad",
            "Puthenvelikkara", "Ramamangalam", "Rayamangalam", "Thiruvaniyoor", "Thirumarady",
            "Thrikkakara", "Thuravoor", "Vadakkekkara", "Vadavukode-Puthencruz", "Varapuzha",
            "Varappetty", "Vazhakulam", "Vengola", "Vengoor"
        ];

        // Thrissur Panchayats
        $thrissurPanchayats = [
            "Adat", "Aloor", "Annamanada", "Anthikad", "Arimpur", "Avanur", "Chazhur",
            "Chelakkara", "Cherpu", "Chowannur", "Desamangalam", "Elavally", "Eriyad",
            "Erumapetty", "Eyyal", "Kadangode", "Kadavallur", "Kaipamangalam", "Kandanassery",
            "Karalam", "Kattakampal", "Kattoor", "Kolazhy", "Koratty", "Kuzhur", "Madakkathara",
            "Mala", "Mathilakam", "Meloor", "Mullassery", "Mullurkara", "Muriyad", "Nadathara",
            "Nattika", "Nenmanikkara", "Orumanayur", "Padiyur", "Pampady", "Pananchery",
            "Paralam", "Parappukkara", "Pavaratty", "Pookode", "Porkulam", "Poyya", "Pulakode",
            "Puthenchira", "Puthur", "Thekkumkara", "Thiruvilwamala", "Thrissur", "Vallathol Nagar",
            "Vadakkekad", "Vadanappally", "Vallachira", "Varandarappilly", "Vellangallur",
            "Vellookkara"
        ];

        // Palakkad Panchayats
        $palakkadPanchayats = [
            "Agali", "Alathur", "Attappady", "Chalavara", "Chalissery", "Elavanchery",
            "Eruthenpathy", "Kadampazhipuram", "Kannambra", "Kappur", "Karakkad", "Karimpuzha",
            "Kattussery", "Kavassery", "Kizhakkenchery", "Kodumbu", "Kollengode", "Kongad",
            "Koppam", "Kottopadam", "Kuzhalmannam", "Lakkidi-Perur", "Malampuzha", "Mankara",
            "Mannarkad", "Mannur", "Marutharode", "Mathur", "Mundur", "Muthalamada", "Nagalassery",
            "Nallepilly", "Nellaya", "Nemmara", "Ongallur", "Ottappalam", "Palakkad", "Pallassana",
            "Parali", "Pattambi", "Pattithara", "Peringottukkurrissi", "Perumatti", "Peruvemba",
            "Pirayiri", "Podannur", "Pookottupadam", "Puthur", "Puthuppariyaram", "Sreekrishnapuram",
            "Tarur", "Thachampara", "Thenkurissi", "Thenkara", "Thirumittakode", "Thiruvegappura",
            "Thrithala", "Vadakarapathy", "Vadavannur", "Vallapuzha", "Vaniyamkulam", "Vilayur"
        ];

        // Malappuram Panchayats
        $malappuramPanchayats = [
            "Alamcode", "Anakkayam", "Angadippuram", "Areekode", "Chelembra", "Chokkad",
            "Edarikkode", "Edayur", "Kalikavu", "Karuvarakundu", "Keezhattur", "Kodur",
            "Kuruva", "Makkaraparamba", "Mancheri", "Mankada", "Maranchery", "Mavoor",
            "Moorkkanad", "Nannamukku", "Nediyiruppu", "Oorakam", "Othukkungal", "Pallikkal",
            "Pandikkad", "Perinthalmanna", "Perumanna-Klari", "Porur", "Pothukal", "Pulamanthole",
            "Puzhakattiri", "Thazhekkode", "Thenhipalam", "Thiruvali", "Thirunavaya", "Tirur",
            "Trikkandiyoor", "Valanchery", "Vazhakkad", "Vazhayur", "Wandoor"
        ];

        // Kozhikode Panchayats
        $kozhikodePanchayats = [
            "Arikkulam", "Balussery", "Chakkittapara", "Changaroth", "Chathamangalam",
            "Chelannur", "Cheruvannur", "Chakkittapara", "Feroke", "Kanthalad", "Kattippara",
            "Kayakkodi", "Kayanna", "Keezhariyur", "Kodanchery", "Koduvally", "Kozhikode",
            "Kunnamangalam", "Kunnummal", "Koyilandy", "Madavoor", "Maruthonkara", "Meppayur",
            "Nadapuram", "Nanminda", "Narippatta", "Olavanna", "Panangad", "Peruvayal",
            "Thiruvambady", "Thurayur", "Thamarassery", "Thikkodi", "Thiruvallur",
            "Unnikulam", "Vadakara", "Valayam", "Vanimel"
        ];

        // Wayanad Panchayats
        $wayanadPanchayats = [
            "Kalpetta", "Kaniyambetta", "Kottathara", "Mananthavady", "Meenangadi",
            "Meppadi", "Moopainad", "Mullankolli", "Muttil", "Nenmeni", "Padinharethara",
            "Pozhuthana", "Pulpally", "Sultan Bathery", "Thavinhal", "Thirunelli",
            "Thondernad", "Vythiri", "Edavaka", "Noolpuzha", "Vellamunda", "Vengappally",
            "Ambalavayal", "Thariode"
        ];

        // Kannur Panchayats
        $kannurPanchayats = [
            "Alakode", "Alapadamba", "Azhikode", "Chengalayi", "Cheruthazham", "Cherukunnu",
            "Chirakkal", "Chittariparamba", "Dharmadam", "Eramam-Kuttoor", "Eruvessy", "Ezhome",
            "Kadannappally-Panapuzha", "Kalliasseri", "Kankol-Alappadamba", "Kannapuram", "Kannur",
            "Karivellur-Peralam", "Kolayad", "Kottayam-Malabar", "Kunnothuparamba", "Kurumathur",
            "Kuthuparamba", "Madayi", "Mangattidam", "Mattannur", "Mayyil", "Mokeri", "Muzhappilangad",
            "Narath", "New Mahe", "Padiyoor", "Panniyannur", "Panoor", "Pathiriyad", "Pattiam",
            "Payyanur", "Peralassery", "Peravoor", "Peringome-Vayakkara", "Pinarayi", "Sreekandapuram",
            "Thalassery", "Thaliparamba", "Thiruvallur", "Udayagiri", "Vengad"
        ];

        // Kasaragod Panchayats
        $kasaragodPanchayats = [
            "Ajanur", "Balal", "Bedadka", "Chengala", "Chemnad", "Delampady", "Enmakaje",
            "Karadka", "Kumbadaje", "Kinanoor-Karinthalam", "Kodom-Belur", "Madhur", "Mogral-Puthur",
            "Muliyar", "Mangalpady", "Nileshwaram", "Padre", "Pallikkara", "Panathady", "Pullur-Periya",
            "Pilicode", "Trikaripur", "Udma", "Valiyaparamba", "Vorkady", "Kallar", "Kayyur-Cheemeni",
            "Kuttikol", "Manjeshwar", "Paivalike", "Puthige", "West Eleri", "East Eleri"
        ];

        // Create all panchayats for each district
        $this->insertPanchayats($districts['thiruvananthapuram']->id, $thiruvananthapuramPanchayats);
        $this->insertPanchayats($districts['kollam']->id, $kollamPanchayats);
        $this->insertPanchayats($districts['pathanamthitta']->id, $pathanamthittaPanchayats);
        $this->insertPanchayats($districts['alappuzha']->id, $alappuzhaPanchayats);
        $this->insertPanchayats($districts['kottayam']->id, $kottayamPanchayats);
        $this->insertPanchayats($districts['idukki']->id, $idukkiPanchayats);
        $this->insertPanchayats($districts['ernakulam']->id, $ernakulamPanchayats);
        $this->insertPanchayats($districts['thrissur']->id, $thrissurPanchayats);
        $this->insertPanchayats($districts['palakkad']->id, $palakkadPanchayats);
        $this->insertPanchayats($districts['malappuram']->id, $malappuramPanchayats);
        $this->insertPanchayats($districts['kozhikode']->id, $kozhikodePanchayats);
        $this->insertPanchayats($districts['wayanad']->id, $wayanadPanchayats);
        $this->insertPanchayats($districts['kannur']->id, $kannurPanchayats);
        $this->insertPanchayats($districts['kasaragod']->id, $kasaragodPanchayats);
    }

    /**
     * Helper function to insert panchayats for a specific district
     *
     * @param int $districtId
     * @param array $panchayats
     * @return void
     */
    private function insertPanchayats($districtId, $panchayats)
    {
        $data = [];
        foreach ($panchayats as $panchayat) {
            $data[] = [
                'district_id' => $districtId,
                'name' => $panchayat,
                'slug' => $this->slugify($panchayat),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid hitting database limits
        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('panchayats')->insert($chunk);
        }
    }

    /**
     * Convert string to slug
     *
     * @param string $text
     * @return string
     */
    private function slugify($text)
    {
        // Replace non letter or digit by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panchayats');
    }

};

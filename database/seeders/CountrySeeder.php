<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $countries = [
            ['name' => 'Afghanistan', 'code' => 'AF', 'phone_code' => '+93'],
            ['name' => 'Albania', 'code' => 'AL', 'phone_code' => '+355'],
            ['name' => 'Algeria', 'code' => 'DZ', 'phone_code' => '+213'],
            ['name' => 'American Samoa', 'code' => 'AS', 'phone_code' => '+1-684'],
            ['name' => 'Andorra', 'code' => 'AD', 'phone_code' => '+376'],
            ['name' => 'Angola', 'code' => 'AO', 'phone_code' => '+244'],
            ['name' => 'Anguilla', 'code' => 'AI', 'phone_code' => '+1-264'],
            ['name' => 'Antarctica', 'code' => 'AQ', 'phone_code' => '+672'],
            ['name' => 'Antigua and Barbuda', 'code' => 'AG', 'phone_code' => '+1-268'],
            ['name' => 'Argentina', 'code' => 'AR', 'phone_code' => '+54'],
            ['name' => 'Armenia', 'code' => 'AM', 'phone_code' => '+374'],
            ['name' => 'Aruba', 'code' => 'AW', 'phone_code' => '+297'],
            ['name' => 'Australia', 'code' => 'AU', 'phone_code' => '+61'],
            ['name' => 'Austria', 'code' => 'AT', 'phone_code' => '+43'],
            ['name' => 'Azerbaijan', 'code' => 'AZ', 'phone_code' => '+994'],
            ['name' => 'Bahamas', 'code' => 'BS', 'phone_code' => '+1-242'],
            ['name' => 'Bahrain', 'code' => 'BH', 'phone_code' => '+973'],
            ['name' => 'Bangladesh', 'code' => 'BD', 'phone_code' => '+880'],
            ['name' => 'Barbados', 'code' => 'BB', 'phone_code' => '+1-246'],
            ['name' => 'Belarus', 'code' => 'BY', 'phone_code' => '+375'],
            ['name' => 'Belgium', 'code' => 'BE', 'phone_code' => '+32'],
            ['name' => 'Belize', 'code' => 'BZ', 'phone_code' => '+501'],
            ['name' => 'Benin', 'code' => 'BJ', 'phone_code' => '+229'],
            ['name' => 'Bermuda', 'code' => 'BM', 'phone_code' => '+1-441'],
            ['name' => 'Bhutan', 'code' => 'BT', 'phone_code' => '+975'],
            ['name' => 'Bolivia', 'code' => 'BO', 'phone_code' => '+591'],
            ['name' => 'Bosnia and Herzegovina', 'code' => 'BA', 'phone_code' => '+387'],
            ['name' => 'Botswana', 'code' => 'BW', 'phone_code' => '+267'],
            ['name' => 'Brazil', 'code' => 'BR', 'phone_code' => '+55'],
            ['name' => 'Brunei Darussalam', 'code' => 'BN', 'phone_code' => '+673'],
            ['name' => 'Bulgaria', 'code' => 'BG', 'phone_code' => '+359'],
            ['name' => 'Burkina Faso', 'code' => 'BF', 'phone_code' => '+226'],
            ['name' => 'Burundi', 'code' => 'BI', 'phone_code' => '+257'],
            ['name' => 'Cambodia', 'code' => 'KH', 'phone_code' => '+855'],
            ['name' => 'Cameroon', 'code' => 'CM', 'phone_code' => '+237'],
            ['name' => 'Canada', 'code' => 'CA', 'phone_code' => '+1'],
            ['name' => 'Cape Verde', 'code' => 'CV', 'phone_code' => '+238'],
            ['name' => 'Cayman Islands', 'code' => 'KY', 'phone_code' => '+1-345'],
            ['name' => 'Central African Republic', 'code' => 'CF', 'phone_code' => '+236'],
            ['name' => 'Chad', 'code' => 'TD', 'phone_code' => '+235'],
            ['name' => 'Chile', 'code' => 'CL', 'phone_code' => '+56'],
            ['name' => 'China', 'code' => 'CN', 'phone_code' => '+86'],
            ['name' => 'Colombia', 'code' => 'CO', 'phone_code' => '+57'],
            ['name' => 'Comoros', 'code' => 'KM', 'phone_code' => '+269'],
            ['name' => 'Congo', 'code' => 'CG', 'phone_code' => '+242'],
            ['name' => 'Congo, The Democratic Republic of the', 'code' => 'CD', 'phone_code' => '+243'],
            ['name' => 'Costa Rica', 'code' => 'CR', 'phone_code' => '+506'],
            ['name' => 'Croatia', 'code' => 'HR', 'phone_code' => '+385'],
            ['name' => 'Cuba', 'code' => 'CU', 'phone_code' => '+53'],
            ['name' => 'Cyprus', 'code' => 'CY', 'phone_code' => '+357'],
            ['name' => 'Czech Republic', 'code' => 'CZ', 'phone_code' => '+420'],
            ['name' => 'Denmark', 'code' => 'DK', 'phone_code' => '+45'],
            ['name' => 'Djibouti', 'code' => 'DJ', 'phone_code' => '+253'],
            ['name' => 'Rwanda', 'code' => 'RW', 'phone_code' => '+250'],
            ['name' => 'Germany', 'code' => 'DE', 'phone_code' => '+49'],
            ['name' => 'Ghana', 'code' => 'GH', 'phone_code' => '+233'],
            ['name' => 'Greece', 'code' => 'GR', 'phone_code' => '+30'],
            ['name' => 'Hungary', 'code' => 'HU', 'phone_code' => '+36'],
            ['name' => 'Iceland', 'code' => 'IS', 'phone_code' => '+354'],
            ['name' => 'India', 'code' => 'IN', 'phone_code' => '+91'],
            ['name' => 'Indonesia', 'code' => 'ID', 'phone_code' => '+62'],
        ];
        Country::insert($countries);
    }
}

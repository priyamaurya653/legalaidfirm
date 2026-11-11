<?php

/*
|--------------------------------------------------------------------------
| Advocate (Admin) registration – dropdown options
|--------------------------------------------------------------------------
| Register form, superadmin create form aur API validation – teeno yahi list use karte hain.
*/

return [

    'genders' => ['Male', 'Female', 'Other'],

    'qualifications' => ['LL.B', 'B.A. LL.B', 'B.B.A. LL.B', 'B.Com LL.B', 'B.Sc LL.B', 'LL.M', 'Ph.D (Law)'],

    'bar_councils' => [
        'Bar Council of Andhra Pradesh',
        'Bar Council of Assam, Nagaland, Meghalaya, Manipur, Tripura, Mizoram & Arunachal Pradesh',
        'Bar Council of Bihar',
        'Bar Council of Chhattisgarh',
        'Bar Council of Delhi',
        'Bar Council of Gujarat',
        'Bar Council of Himachal Pradesh',
        'Bar Council of Jammu & Kashmir and Ladakh',
        'Bar Council of Jharkhand',
        'Bar Council of Karnataka',
        'Bar Council of Kerala',
        'Bar Council of Madhya Pradesh',
        'Bar Council of Maharashtra & Goa',
        'Bar Council of Odisha',
        'Bar Council of Punjab & Haryana',
        'Bar Council of Rajasthan',
        'Bar Council of Tamil Nadu & Puducherry',
        'Bar Council of Telangana',
        'Bar Council of Uttar Pradesh',
        'Bar Council of Uttarakhand',
        'Bar Council of West Bengal',
    ],

    'states' => [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
        'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
        'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh',
        'Uttarakhand', 'West Bengal',
        'Andaman & Nicobar Islands', 'Chandigarh', 'Dadra & Nagar Haveli and Daman & Diu',
        'Delhi', 'Jammu & Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry',
    ],

    'id_proofs' => ['Aadhaar Card', 'PAN Card', 'Voter ID', 'Passport', 'Driving Licence'],

    'statuses' => ['active', 'pending', 'inactive'],

    /*
    | Website se register hone wala advocate (admin) turant login kar sake?
    | true  = register hote hi "active" – apni email/password se login
    | false = "pending" – superadmin approve karega tab login
    | .env : ADVOCATE_AUTO_APPROVE=true
    */
    'auto_approve' => env('ADVOCATE_AUTO_APPROVE', true),
];
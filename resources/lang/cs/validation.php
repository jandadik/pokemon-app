<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validační překlady
    |--------------------------------------------------------------------------
    |
    | Následující jazykové řádky obsahují výchozí chybové zprávy používané
    | validačními třídami. Některá z těchto pravidel mají více verzí,
    | například velikost. Můžete zde měnit každou ze zpráv.
    |
    */

    'accepted' => ':attribute musí být přijat.',
    'accepted_if' => ':attribute musí být přijat, když :other je :value.',
    'active_url' => ':attribute není platnou URL adresou.',
    'after' => ':attribute musí být datum po :date.',
    'after_or_equal' => ':attribute musí být datum po nebo rovno :date.',
    'alpha' => ':attribute může obsahovat pouze písmena.',
    'alpha_dash' => ':attribute může obsahovat pouze písmena, čísla, pomlčky a podtržítka.',
    'alpha_num' => ':attribute může obsahovat pouze písmena a čísla.',
    'array' => ':attribute musí být pole.',
    'before' => ':attribute musí být datum před :date.',
    'before_or_equal' => ':attribute musí být datum před nebo rovno :date.',
    'between' => [
        'numeric' => ':attribute musí být mezi :min a :max.',
        'file' => ':attribute musí být mezi :min a :max kilobajty.',
        'string' => ':attribute musí být mezi :min a :max znaky.',
        'array' => ':attribute musí mít mezi :min a :max položkami.',
    ],
    'boolean' => ':attribute pole musí být true nebo false.',
    'confirmed' => ':attribute potvrzení se neshoduje.',
    'current_password' => 'Heslo je nesprávné.',
    'date' => ':attribute není platné datum.',
    'date_equals' => ':attribute musí být datum rovno :date.',
    'date_format' => ':attribute neodpovídá formátu :format.',
    'declined' => ':attribute musí být odmítnut.',
    'declined_if' => ':attribute musí být odmítnut, když :other je :value.',
    'different' => ':attribute a :other se musí lišit.',
    'digits' => ':attribute musí být :digits číslic.',
    'digits_between' => ':attribute musí být mezi :min a :max číslicemi.',
    'dimensions' => ':attribute má neplatné rozměry obrázku.',
    'distinct' => ':attribute pole má duplicitní hodnotu.',
    'email' => ':attribute musí být platná e-mailová adresa.',
    'ends_with' => ':attribute musí končit jedním z následujících: :values.',
    'exists' => 'Vybraný :attribute je neplatný.',
    'file' => ':attribute musí být soubor.',
    'filled' => ':attribute pole musí mít hodnotu.',
    'gt' => [
        'numeric' => ':attribute musí být větší než :value.',
        'file' => ':attribute musí být větší než :value kilobajtů.',
        'string' => ':attribute musí být větší než :value znaků.',
        'array' => ':attribute musí mít více než :value položek.',
    ],
    'gte' => [
        'numeric' => ':attribute musí být větší nebo rovno :value.',
        'file' => ':attribute musí být větší nebo rovno :value kilobajtů.',
        'string' => ':attribute musí být větší nebo rovno :value znaků.',
        'array' => ':attribute musí mít :value položek nebo více.',
    ],
    'image' => ':attribute musí být obrázek.',
    'in' => 'Vybraný :attribute je neplatný.',
    'in_array' => ':attribute pole neexistuje v :other.',
    'integer' => ':attribute musí být celé číslo.',
    'ip' => ':attribute musí být platná IP adresa.',
    'ipv4' => ':attribute musí být platná IPv4 adresa.',
    'ipv6' => ':attribute musí být platná IPv6 adresa.',
    'json' => ':attribute musí být platný JSON řetězec.',
    'lt' => [
        'numeric' => ':attribute musí být menší než :value.',
        'file' => ':attribute musí být menší než :value kilobajtů.',
        'string' => ':attribute musí být menší než :value znaků.',
        'array' => ':attribute musí mít méně než :value položek.',
    ],
    'lte' => [
        'numeric' => ':attribute musí být menší nebo rovno :value.',
        'file' => ':attribute musí být menší nebo rovno :value kilobajtů.',
        'string' => ':attribute musí být menší nebo rovno :value znaků.',
        'array' => ':attribute nesmí mít více než :value položek.',
    ],
    'max' => [
        'numeric' => ':attribute nesmí být větší než :max.',
        'file' => ':attribute nesmí být větší než :max kilobajtů.',
        'string' => ':attribute nesmí být větší než :max znaků.',
        'array' => ':attribute nesmí mít více než :max položek.',
    ],
    'mimes' => ':attribute musí být soubor typu: :values.',
    'mimetypes' => ':attribute musí být soubor typu: :values.',
    'min' => [
        'numeric' => ':attribute musí být alespoň :min.',
        'file' => ':attribute musí být alespoň :min kilobajtů.',
        'string' => ':attribute musí být alespoň :min znaků.',
        'array' => ':attribute musí mít alespoň :min položek.',
    ],
    'multiple_of' => ':attribute musí být násobek :value.',
    'not_in' => 'Vybraný :attribute je neplatný.',
    'not_regex' => ':attribute formát je neplatný.',
    'numeric' => ':attribute musí být číslo.',
    'password' => 'Heslo je nesprávné.',
    'present' => ':attribute pole musí být přítomno.',
    'prohibited' => ':attribute pole je zakázáno.',
    'prohibited_if' => ':attribute pole je zakázáno, když :other je :value.',
    'prohibited_unless' => ':attribute pole je zakázáno, pokud :other není v :values.',
    'prohibits' => ':attribute pole zakazuje přítomnost :other.',
    'regex' => ':attribute formát je neplatný.',
    'required' => ':attribute pole je povinné.',
    'required_if' => ':attribute pole je povinné, když :other je :value.',
    'required_unless' => ':attribute pole je povinné, pokud :other není v :values.',
    'required_with' => ':attribute pole je povinné, když :values je přítomno.',
    'required_with_all' => ':attribute pole je povinné, když :values jsou přítomny.',
    'required_without' => ':attribute pole je povinné, když :values není přítomno.',
    'required_without_all' => ':attribute pole je povinné, když žádná z :values není přítomna.',
    'same' => ':attribute a :other se musí shodovat.',
    'size' => [
        'numeric' => ':attribute musí být :size.',
        'file' => ':attribute musí být :size kilobajtů.',
        'string' => ':attribute musí být :size znaků.',
        'array' => ':attribute musí obsahovat :size položek.',
    ],
    'starts_with' => ':attribute musí začínat jedním z následujících: :values.',
    'string' => ':attribute musí být řetězec.',
    'timezone' => ':attribute musí být platná časová zóna.',
    'unique' => ':attribute již byl použit.',
    'uploaded' => ':attribute se nepodařilo nahrát.',
    'url' => ':attribute musí být platná URL.',
    'uuid' => ':attribute musí být platné UUID.',

    /*
    |--------------------------------------------------------------------------
    | Vlastní validační zprávy
    |--------------------------------------------------------------------------
    |
    | Zde můžete specifikovat vlastní validační zprávy pro atributy použitím
    | konvence "attribute.rule" pro pojmenování řádků. To umožňuje rychle
    | specifikovat konkrétní vlastní jazykovou zprávu pro dané pravidlo atributu.
    |
    */

    'custom' => [
        'condition' => [
            'condition_enum' => 'Stav karty musí být jedna z hodnot: Near Mint, Excellent, Good, Played, Poor.',
        ],
        'language' => [
            'language_enum' => 'Jazyk musí být jeden z podporovaných: English, Czech, German, French, Japanese.',
        ],
        'grade_value' => [
            'grading_required_with' => 'Hodnota gradingu je povinná při vyplnění gradovací společnosti.',
        ],
        'grade_company' => [
            'grading_required_with' => 'Gradovací společnost je povinná při vyplnění hodnoty gradingu.',
        ],
        'grading' => [
            'grading_required_with' => 'Gradovací společnost je povinná při vyplnění hodnoty gradingu.',
        ],
        'grading_cert' => [
            'grading_required_with' => 'Hodnota gradingu je povinná při vyplnění gradovací společnosti.',
        ],
        'variant_id' => [
            'valid_card_variant_combination' => 'Vybraná varianta nepatří k této kartě.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vlastní validační atributy
    |--------------------------------------------------------------------------
    |
    | Následující jazykové řádky se používají k nahrazení našeho zástupného textu
    | atributu, jako je "e-mail" namísto "email". To nám pomáhá
    | naše zprávy více vyjadřující.
    |
    */

    'attributes' => [
        'card_id' => 'karta',
        'variant_id' => 'varianta karty',
        'condition' => 'stav karty',
        'language' => 'jazyk',
        'grade_company' => 'gradovací společnost',
        'grade_value' => 'hodnota gradingu',
        'is_first_edition' => 'první edice',
        'is_graded' => 'gradováno',
        'collection_id' => 'kolekce',
        'variant_type' => 'typ varianty',
        'purchase_date' => 'datum nákupu',
        'notes' => 'poznámky',
        'purchase_price' => 'nákupní cena',
        'quantity' => 'množství',
        'location' => 'umístění',
        'note' => 'poznámka',
        'first_edition' => 'první edice',
        'grading' => 'gradovací společnost',
        'grading_cert' => 'hodnota gradingu',
    ],
]; 
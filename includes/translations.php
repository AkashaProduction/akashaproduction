<?php
declare(strict_types=1);

/**
 * Akasha Production — translation overrides on top of the French defaults.
 *
 * Each entry is a partial override (deep-merged with app_content_defaults()).
 * Keys not translated for a given language fall back to the French default.
 *
 * Card descriptions for external sites are translated even though the target
 * websites themselves remain in their own languages — the visitor still gets
 * the description in their preferred language and can decide to follow the link.
 */

function app_content_translations(): array
{
    return [

        // ─────────────────────────── EN — English ───────────────────────────
        'en' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Inter Connected Creativity',
                'meta_description' => 'Akasha Production crafts bespoke web creations, interactive worlds and narrative games at the crossroads of the sensitive and the conceptual.',
                'enter_label'      => 'Enter',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'Featured',
                'creations' => 'Our creations',
                'projects'  => 'Your projects',
                'contact'   => 'Contact',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Inter Connected Creativity',
                'lead'           => 'Akasha Production weaves web productions of every form — conceptual sites, community platforms, narrative games and interactive works. Each project is conceived as a constellation: linked intentions, a coherent aesthetic, an experience that endures.',
                'cta_creations'  => 'Our creations',
                'cta_projects'   => 'Your projects',
            ],
            'featured' => [
                'eyebrow'     => 'Featured',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia is a paradoxical online escape game — a living work you enter out of curiosity, only to discover, while crossing nine circles, that there is nothing to flee from: the room you seek the exit of is presence itself. Three passages, twenty kinds of cards, five rituals. An initiatory experience, distributable, conceived as a narrative constellation.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Play Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'Portfolio',
                'title'   => 'Our creations',
                'intro'   => 'A gallery of web projects delivered or in orbit — each with a light of its own.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Narrative and social universe around “Shadows & Lights” — community-driven gameplay, symbolic economy and signature graphic identity.'],
                    ['name' => 'CMS-Source.org',         'description' => 'A modular, distributable CMS designed to build fluid, configurable web architectures — the software foundation of Akasha editions.'],
                    ['name' => 'Permatheque.org',        'description' => 'A living library of permaculture and sensitive autonomy knowledge — organised like a digital edible forest.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Online Ayurvedic consulting platform — a soothed interface, a guided journey and editorial writing in service of care.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Magazine and notebook of autonomous-living experiments — articles, practical sheets, learning paths and an open community.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'An eclectic learning space — shared knowledge, micro-formats, free exploration. A daily atelier of curiosity.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Moroccan real-estate atlas — tools to support investment, settling in and patrimony enhancement.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'A network of holistic practitioners and resources — directory, calendar, editorial content and a community space.'],
                    ['name' => 'Relief.Education',       'description' => 'An educational platform in orbit — a narrative design system and pedagogical architecture under construction.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'Your projects',
                'title'             => 'A showcase for your creations',
                'empty_state'       => 'No project is currently featured.',
                'intro'             => 'Akasha Production opens a space to promote innovative web creations.',
                'cms_source_lead'   => 'Looking for support to bring it to life? Visit our',
                'cms_source_link'   => 'creators’ space at CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Join the constellation: submit your project',
                'submit_label'      => 'Send the project',
                'load_more_label'   => 'See more',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Legal information',
                'contact_link' => 'Contact us',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, France',
                    'Phone: +33 7 81 42 88 71',
                    'Hosting: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, France',
                ],
            ],
            'contact' => [
                'title'        => 'Contact us',
                'lead'         => 'For a project, a collaboration or a simple question — leave us a message, we reply within 48 hours.',
                'submit_label' => 'Send',
            ],
        ],

        // ─────────────────────────── ES — Español ───────────────────────────
        'es' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Creatividad Inter Conectada',
                'meta_description' => 'Akasha Production crea producciones web a medida, universos interactivos y juegos narrativos en el cruce de lo sensible y lo conceptual.',
                'enter_label'      => 'Entrar',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'Destacado',
                'creations' => 'Nuestras creaciones',
                'projects'  => 'Tus proyectos',
                'contact'   => 'Contacto',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Creatividad Inter Conectada',
                'lead'           => 'Akasha Production teje producciones web de todos los formatos — sitios conceptuales, plataformas comunitarias, juegos narrativos y obras interactivas. Cada proyecto se concibe como una constelación: intenciones enlazadas, una estética coherente, una experiencia que perdura.',
                'cta_creations'  => 'Nuestras creaciones',
                'cta_projects'   => 'Tus proyectos',
            ],
            'featured' => [
                'eyebrow'     => 'Destacado',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia es un escape game paradójico en línea — una obra viva en la que se entra por curiosidad y donde se descubre, recorriendo nueve círculos, que no hay nada de lo que huir: la sala cuya salida buscas es presencia. Tres pasos, veinte formas de cartas, cinco rituales. Una experiencia iniciática distribuible, pensada como una constelación narrativa.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Jugar a Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'Portafolio',
                'title'   => 'Nuestras creaciones',
                'intro'   => 'Una galería de proyectos web entregados o en órbita — cada uno con su propia luz.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Universo narrativo y social en torno a “Sombras y Luces” — jugabilidad comunitaria, economía simbólica e identidad gráfica única.'],
                    ['name' => 'CMS-Source.org',         'description' => 'CMS modular y distribuible, diseñado para construir arquitecturas web fluidas y configurables — la base de software de las ediciones Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'Biblioteca viva de saberes de permacultura y autonomía sensible — organizada como un bosque comestible digital.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Plataforma de consulta ayurvédica en línea — interfaz serena, recorrido guiado y escritura editorial al servicio del cuidado.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Revista y cuaderno de experiencias sobre la vida autónoma — artículos, fichas prácticas, sendas de aprendizaje y comunidad abierta.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'Un espacio pedagógico ecléctico — saberes compartidos, microformatos, exploración libre. Un taller de curiosidad cotidiana.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Atlas inmobiliario marroquí — herramientas para acompañar la inversión, la instalación y la valorización del patrimonio.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'Red de practicantes y recursos holísticos — directorio, agenda, contenidos editoriales y espacio comunitario.'],
                    ['name' => 'Relief.Education',       'description' => 'Plataforma educativa en órbita — sistema de diseño narrativo y arquitectura pedagógica en construcción.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'Tus proyectos',
                'title'             => 'Una vitrina para tus creaciones',
                'empty_state'       => 'Actualmente no hay ningún proyecto destacado.',
                'intro'             => 'Akasha Production abre un espacio de promoción para creaciones web innovadoras.',
                'cms_source_lead'   => '¿Buscas apoyo para realizarlo? Visita nuestro',
                'cms_source_link'   => 'espacio creativo en CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Únete a la constelación: envía tu proyecto',
                'submit_label'      => 'Enviar el proyecto',
                'load_more_label'   => 'Ver más',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Información legal',
                'contact_link' => 'Contáctanos',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, Francia',
                    'Teléfono: +33 7 81 42 88 71',
                    'Alojamiento: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, Francia',
                ],
            ],
            'contact' => [
                'title'        => 'Contáctanos',
                'lead'         => 'Para un proyecto, una colaboración o una simple pregunta — déjanos un mensaje, respondemos en menos de 48 horas.',
                'submit_label' => 'Enviar',
            ],
        ],

        // ─────────────────────────── RU — Русский ───────────────────────────
        'ru' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Взаимосвязанная креативность',
                'meta_description' => 'Akasha Production создаёт авторские веб-проекты, интерактивные миры и нарративные игры на стыке чувственного и концептуального.',
                'enter_label'      => 'Войти',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'Главное',
                'creations' => 'Наши работы',
                'projects'  => 'Ваши проекты',
                'contact'   => 'Контакты',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Взаимосвязанная креативность',
                'lead'           => 'Akasha Production создаёт веб-произведения всех форматов — концептуальные сайты, сообщества, нарративные игры и интерактивные работы. Каждый проект задуман как созвездие: связанные намерения, целостная эстетика, опыт, который остаётся.',
                'cta_creations'  => 'Наши работы',
                'cta_projects'   => 'Ваши проекты',
            ],
            'featured' => [
                'eyebrow'     => 'Главное',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia — парадоксальный онлайн-эскейп — живое произведение, в которое входишь из любопытства и обнаруживаешь, проходя девять кругов, что бежать некуда: комната, выход из которой ищешь, — это присутствие. Три прохода, двадцать видов карт, пять ритуалов. Инициатический, распределяемый опыт, задуманный как нарративное созвездие.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Играть в Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'Портфолио',
                'title'   => 'Наши работы',
                'intro'   => 'Галерея веб-проектов, выпущенных или находящихся на орбите — каждый со своим светом.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Нарративная и социальная вселенная вокруг «Теней и Света» — коммьюнити-геймплей, символическая экономика и фирменный графический язык.'],
                    ['name' => 'CMS-Source.org',         'description' => 'Модульная распространяемая CMS для создания гибких и настраиваемых веб-архитектур — программный фундамент изданий Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'Живая библиотека знаний о пермакультуре и тонкой автономии — организована как цифровой съедобный лес.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Платформа онлайн-консультаций по аюрведе — спокойный интерфейс, ведомый путь и редакционная забота о пользователе.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Журнал и блокнот опыта автономной жизни — статьи, практические карточки, обучающие маршруты и открытое сообщество.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'Эклектичное образовательное пространство — общие знания, микроформаты, свободное исследование. Ежедневная мастерская любопытства.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Марокканский атлас недвижимости — инструменты сопровождения инвестиций, переезда и развития имущества.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'Сеть холистических практиков и ресурсов — каталог, календарь, редакционные материалы и сообщество.'],
                    ['name' => 'Relief.Education',       'description' => 'Образовательная платформа на стартовой орбите — нарративная дизайн-система и педагогическая архитектура в стадии сборки.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'Ваши проекты',
                'title'             => 'Витрина для ваших работ',
                'empty_state'       => 'Пока ни один проект не выделен.',
                'intro'             => 'Akasha Production открывает пространство продвижения инновационных веб-творений.',
                'cms_source_lead'   => 'Ищете поддержку в реализации? Загляните в наше',
                'cms_source_link'   => 'творческое пространство на CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Присоединяйтесь к созвездию: отправьте свой проект',
                'submit_label'      => 'Отправить проект',
                'load_more_label'   => 'Показать ещё',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Юридическая информация',
                'contact_link' => 'Связаться с нами',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, Франция',
                    'Телефон: +33 7 81 42 88 71',
                    'Хостинг: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, Франция',
                ],
            ],
            'contact' => [
                'title'        => 'Связаться с нами',
                'lead'         => 'По проекту, сотрудничеству или простому вопросу — оставьте сообщение, мы ответим в течение 48 часов.',
                'submit_label' => 'Отправить',
            ],
        ],

        // ─────────────────────────── AR — العربية (RTL) ───────────────────────────
        'ar' => [
            'site' => [
                'meta_title'       => 'Akasha Production — إبداع متشابك',
                'meta_description' => 'تنسج Akasha Production أعمالاً ويب مخصصة، وعوالم تفاعلية، وألعاباً سردية على مفترق الحساس والمفاهيمي.',
                'enter_label'      => 'دخول',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'مميّز',
                'creations' => 'أعمالنا',
                'projects'  => 'مشاريعكم',
                'contact'   => 'تواصل',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'إبداع متشابك',
                'lead'           => 'تنسج Akasha Production إنتاجات ويب من جميع الأشكال — مواقع مفاهيمية، منصّات مجتمعية، ألعاب سردية وأعمال تفاعلية. كل مشروع مفكَّر فيه كمنظومة نجمية: نوايا متّصلة، جمالية متّسقة، وتجربة تَدُوم.',
                'cta_creations'  => 'أعمالنا',
                'cta_projects'   => 'مشاريعكم',
            ],
            'featured' => [
                'eyebrow'     => 'مميّز',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "ألِيثْيا لعبة هروب متناقضة عبر الإنترنت — عمل حيّ يدخله المرء بدافع الفضول ليكتشف، عابِراً تسع دوائر، أنه لا شيء للهروب منه: الغرفة التي يبحث عن مخرجها هي الحضور ذاته. ثلاثة معابر، عشرون شكلاً من البطاقات، خمسة طقوس. تجربة استنارة قابلة للتوزيع، مفكَّر فيها كمنظومة سردية.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'العب Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'الأعمال',
                'title'   => 'أعمالنا',
                'intro'   => 'معرضٌ لمشاريع ويب أُنجزت أو في مدارها — لكلٍّ منها ضوءه الخاص.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'كون سردي واجتماعي حول "الظلال والأنوار" — لعب جماعي، اقتصاد رمزي وهوية بصرية مميّزة.'],
                    ['name' => 'CMS-Source.org',         'description' => 'نظام إدارة محتوى نمطي قابل للتوزيع، صُمِّم لبناء بنى ويب مرنة وقابلة للضبط — الأساس البرمجي لإصدارات Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'مكتبة حيّة لمعارف البِرْماكَلْتشر والاستقلال الحساس — منظّمة كغابة رقمية صالحة للأكل.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'منصّة استشارة آيورفيدا عبر الإنترنت — واجهة هادئة، مسار موجَّه وكتابة تحريرية في خدمة الرعاية.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'مجلة ودفتر تجارب عن الحياة المستقلة — مقالات، بطاقات عملية، مسارات تعلّم ومجتمع مفتوح.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'فضاء تربوي متنوّع — معارف مشتركة، صيغ مصغّرة، استكشاف حر. ورشة فضول يومية.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'أطلس عقاري مغربي — أدوات لمرافقة الاستثمار والاستقرار وتثمين الممتلكات.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'شبكة من الممارسين والموارد الشاملة — دليل، أجندة، محتوى تحريري وفضاء مجتمعي.'],
                    ['name' => 'Relief.Education',       'description' => 'منصّة تعليمية في المدار — نظام تصميم سردي وهندسة بيداغوجية قيد البناء.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'مشاريعكم',
                'title'             => 'واجهة عرض لإبداعاتكم',
                'empty_state'       => 'لا يوجد حالياً أي مشروع مُسلَّط الضوء عليه.',
                'intro'             => 'تفتح Akasha Production فضاءً للترويج للإبداعات الويب المبتكَرة.',
                'cms_source_lead'   => 'هل تبحث عن دعم لإنجاز مشروعك؟ زُر',
                'cms_source_link'   => 'فضاءنا الإبداعي على CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'انضمّ إلى المنظومة: قدِّم مشروعك',
                'submit_label'      => 'إرسال المشروع',
                'load_more_label'   => 'عرض المزيد',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'معلومات قانونية',
                'contact_link' => 'تواصل معنا',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, فرنسا',
                    'الهاتف: +33 7 81 42 88 71',
                    'الاستضافة: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, فرنسا',
                ],
            ],
            'contact' => [
                'title'        => 'تواصل معنا',
                'lead'         => 'لمشروع، تعاون أو سؤال بسيط — اترك لنا رسالة، نردّ خلال أقل من 48 ساعة.',
                'submit_label' => 'إرسال',
            ],
        ],

        // ─────────────────────────── ZH — 中文 ───────────────────────────
        'zh' => [
            'site' => [
                'meta_title'       => 'Akasha Production — 互联创造力',
                'meta_description' => 'Akasha Production 打造定制化的网络作品、互动世界与叙事游戏，连接感性与概念的交汇之处。',
                'enter_label'      => '进入',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => '精选',
                'creations' => '我们的作品',
                'projects'  => '您的项目',
                'contact'   => '联系',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => '互联创造力',
                'lead'           => 'Akasha Production 编织各种形式的网络作品 —— 概念站点、社区平台、叙事游戏与互动作品。每一个项目都被视为一个星座：相互联结的意图、统一的美学、留存于心的体验。',
                'cta_creations'  => '我们的作品',
                'cta_projects'   => '您的项目',
            ],
            'featured' => [
                'eyebrow'     => '精选',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia 是一款矛盾式的在线密室游戏 —— 一件鲜活的作品，玩家因好奇而进入，穿越九个圆环之后会发现：无须逃离，所谓出口的房间，正是当下之在。三段通道、二十种卡牌、五次仪式。一个可分发的启蒙体验，构想为一座叙事星座。",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => '开始游戏 Aletheia',
            ],
            'creations' => [
                'eyebrow' => '作品集',
                'title'   => '我们的作品',
                'intro'   => '已交付或仍在轨道上的网络项目画廊 —— 每个都有自己独特的光。',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => '围绕「光与影」的叙事与社交宇宙 —— 以社区为核心的玩法、象征性经济与签名式视觉风格。'],
                    ['name' => 'CMS-Source.org',         'description' => '模块化、可分发的内容管理系统，旨在构建灵活、可配置的网络架构 —— 是 Akasha 系列出版物的软件基石。'],
                    ['name' => 'Permatheque.org',        'description' => '关于朴门永续与细腻自治的活态知识库 —— 像一片可食用的数字森林般组织。'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => '在线阿育吠陀咨询平台 —— 沉静的界面、引导式路径与服务于关怀的编辑写作。'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => '关于自治生活的杂志与经验手册 —— 文章、实操卡片、学习路径与开放社群。'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => '一个折衷的教学空间 —— 共享的知识、微格式与自由探索。一间日常的好奇心工作室。'],
                    ['name' => 'Atlas-Access.immo',      'description' => '摩洛哥地产地图册 —— 协助投资、定居与遗产价值提升的工具。'],
                    ['name' => 'Harmonie-Holistique.org','description' => '整体疗法从业者与资源网络 —— 名录、日程、编辑内容与社区空间。'],
                    ['name' => 'Relief.Education',       'description' => '一座正在轨道上的教育平台 —— 叙事式设计系统与教学架构正在构建中。'],
                ],
            ],
            'projects' => [
                'eyebrow'           => '您的项目',
                'title'             => '为您的创作提供展示位',
                'empty_state'       => '当前没有项目被推荐。',
                'intro'             => 'Akasha Production 开放一个推广创新网络作品的空间。',
                'cms_source_lead'   => '在寻找实现项目的支持？请访问我们的',
                'cms_source_link'   => 'CMS-Source.org 创作空间',
                'instructions'      => '',
                'form_title'        => '加入星座：提交您的项目',
                'submit_label'      => '发送项目',
                'load_more_label'   => '查看更多',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => '法律信息',
                'contact_link' => '联系我们',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, 法国',
                    '电话：+33 7 81 42 88 71',
                    '主机：o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, 法国',
                ],
            ],
            'contact' => [
                'title'        => '联系我们',
                'lead'         => '无论是项目、合作还是简单的提问 —— 留下一条信息，我们会在 48 小时内回复。',
                'submit_label' => '发送',
            ],
        ],

        // ─────────────────────────── PT — Português ───────────────────────────
        'pt' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Criatividade Inter Conectada',
                'meta_description' => 'A Akasha Production cria produções web à medida, universos interativos e jogos narrativos no cruzamento do sensível e do conceptual.',
                'enter_label'      => 'Entrar',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'Em destaque',
                'creations' => 'As nossas criações',
                'projects'  => 'Os teus projetos',
                'contact'   => 'Contacto',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Criatividade Inter Conectada',
                'lead'           => 'A Akasha Production tece produções web de todos os formatos — sites concetuais, plataformas comunitárias, jogos narrativos e obras interativas. Cada projeto é pensado como uma constelação: intenções ligadas, uma estética coerente, uma experiência que permanece.',
                'cta_creations'  => 'As nossas criações',
                'cta_projects'   => 'Os teus projetos',
            ],
            'featured' => [
                'eyebrow'     => 'Em destaque',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia é um escape game paradoxal online — uma obra viva onde se entra por curiosidade e se descobre, percorrendo nove círculos, que não há nada de que fugir: a sala cuja saída procuras é presença. Três passagens, vinte formas de cartas, cinco rituais. Uma experiência iniciática distribuível, pensada como uma constelação narrativa.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Jogar Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'Portefólio',
                'title'   => 'As nossas criações',
                'intro'   => 'Uma galeria de projetos web entregues ou em órbita — cada um com a sua luz própria.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Universo narrativo e social em torno de “Sombras e Luzes” — jogabilidade comunitária, economia simbólica e identidade gráfica única.'],
                    ['name' => 'CMS-Source.org',         'description' => 'CMS modular e distribuível, pensado para erguer arquiteturas web fluidas e configuráveis — a base de software das edições Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'Biblioteca viva de saberes da permacultura e da autonomia sensível — organizada como uma floresta comestível digital.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Plataforma de consulta ayurvédica online — interface serena, percurso guiado e escrita editorial ao serviço do cuidado.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Revista e caderno de experiências sobre a vida autónoma — artigos, fichas práticas, percursos de aprendizagem e comunidade aberta.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'Um espaço pedagógico ecléctico — saberes partilhados, microformatos, exploração livre. Um atelier de curiosidade diária.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Atlas imobiliário marroquino — ferramentas para acompanhar o investimento, a instalação e a valorização do património.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'Rede de praticantes e de recursos holísticos — diretório, agenda, conteúdos editoriais e espaço de comunidade.'],
                    ['name' => 'Relief.Education',       'description' => 'Plataforma educativa em órbita — sistema de design narrativo e arquitetura pedagógica em construção.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'Os teus projetos',
                'title'             => 'Uma vitrina para as tuas criações',
                'empty_state'       => 'Nenhum projeto está atualmente em destaque.',
                'intro'             => 'A Akasha Production abre um espaço de promoção para criações web inovadoras.',
                'cms_source_lead'   => 'Procuras apoio para o concretizar? Visita o nosso',
                'cms_source_link'   => 'espaço criativo no CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Junta-te à constelação: submete o teu projeto',
                'submit_label'      => 'Enviar o projeto',
                'load_more_label'   => 'Ver mais',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Informações legais',
                'contact_link' => 'Contacta-nos',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, França',
                    'Telefone: +33 7 81 42 88 71',
                    'Alojamento: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, França',
                ],
            ],
            'contact' => [
                'title'        => 'Contacta-nos',
                'lead'         => 'Para um projeto, uma colaboração ou uma simples pergunta — deixa-nos uma mensagem, respondemos em menos de 48 horas.',
                'submit_label' => 'Enviar',
            ],
        ],

        // ─────────────────────────── PL — Polski ───────────────────────────
        'pl' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Wzajemnie Połączona Kreatywność',
                'meta_description' => 'Akasha Production tworzy autorskie produkcje internetowe, interaktywne światy i gry narracyjne na styku wrażliwości i koncepcji.',
                'enter_label'      => 'Wejdź',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'Polecane',
                'creations' => 'Nasze realizacje',
                'projects'  => 'Wasze projekty',
                'contact'   => 'Kontakt',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Wzajemnie Połączona Kreatywność',
                'lead'           => 'Akasha Production splata produkcje internetowe wszelkich form — strony konceptualne, platformy społecznościowe, gry narracyjne i dzieła interaktywne. Każdy projekt jest pomyślany jak konstelacja: powiązane intencje, spójna estetyka, doświadczenie, które pozostaje.',
                'cta_creations'  => 'Nasze realizacje',
                'cta_projects'   => 'Wasze projekty',
            ],
            'featured' => [
                'eyebrow'     => 'Polecane',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia to paradoksalny escape room online — żywe dzieło, do którego wchodzi się z ciekawości, by odkryć, przemierzając dziewięć kręgów, że nie ma przed czym uciekać: pokój, którego wyjścia szukasz, to obecność. Trzy przejścia, dwadzieścia rodzajów kart, pięć rytuałów. Inicjacyjne, dystrybuowalne doświadczenie, pomyślane jako narracyjna konstelacja.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Zagraj w Aletheię',
            ],
            'creations' => [
                'eyebrow' => 'Portfolio',
                'title'   => 'Nasze realizacje',
                'intro'   => 'Galeria projektów internetowych zrealizowanych lub na orbicie — każdy z własnym światłem.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Narracyjne i społeczne uniwersum wokół „Cieni i Świateł” — rozgrywka społecznościowa, symboliczna ekonomia i charakterystyczna identyfikacja graficzna.'],
                    ['name' => 'CMS-Source.org',         'description' => 'Modułowy, dystrybuowalny CMS do budowania płynnych, konfigurowalnych architektur webowych — programowy fundament wydań Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'Żywa biblioteka wiedzy o permakulturze i wrażliwej autonomii — zorganizowana jak cyfrowy las jadalny.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Platforma konsultacji ajurwedyjskich online — wyciszony interfejs, prowadzona ścieżka i pisanie redakcyjne w służbie troski.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Magazyn i notatnik doświadczeń życia w autonomii — artykuły, karty praktyczne, ścieżki nauki i otwarta społeczność.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'Eklektyczna przestrzeń edukacyjna — wspólna wiedza, mikroformaty, swobodne odkrywanie. Codzienna pracownia ciekawości.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Marokański atlas nieruchomości — narzędzia wsparcia inwestycji, osiedlenia i waloryzacji majątku.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'Sieć praktyków i zasobów holistycznych — katalog, kalendarz, treści redakcyjne i przestrzeń społeczności.'],
                    ['name' => 'Relief.Education',       'description' => 'Platforma edukacyjna na orbicie — narracyjny system designu i architektura pedagogiczna w trakcie budowy.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'Wasze projekty',
                'title'             => 'Witryna dla waszych dzieł',
                'empty_state'       => 'Aktualnie żaden projekt nie jest wyróżniony.',
                'intro'             => 'Akasha Production otwiera przestrzeń promocji innowacyjnych dzieł internetowych.',
                'cms_source_lead'   => 'Szukasz wsparcia, by go zrealizować? Odwiedź naszą',
                'cms_source_link'   => 'przestrzeń twórczą na CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Dołącz do konstelacji: prześlij swój projekt',
                'submit_label'      => 'Wyślij projekt',
                'load_more_label'   => 'Zobacz więcej',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Informacje prawne',
                'contact_link' => 'Skontaktuj się',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, Francja',
                    'Telefon: +33 7 81 42 88 71',
                    'Hosting: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, Francja',
                ],
            ],
            'contact' => [
                'title'        => 'Skontaktuj się',
                'lead'         => 'W sprawie projektu, współpracy lub zwykłego pytania — zostaw wiadomość, odpowiadamy w mniej niż 48 godzin.',
                'submit_label' => 'Wyślij',
            ],
        ],

        // ─────────────────────────── IT — Italiano ───────────────────────────
        'it' => [
            'site' => [
                'meta_title'       => 'Akasha Production — Creatività Inter Connessa',
                'meta_description' => 'Akasha Production realizza produzioni web su misura, universi interattivi e giochi narrativi al crocevia tra il sensibile e il concettuale.',
                'enter_label'      => 'Entrare',
                'loader_label'     => 'Akasha Production',
            ],
            'nav' => [
                'featured'  => 'In primo piano',
                'creations' => 'Le nostre creazioni',
                'projects'  => 'I tuoi progetti',
                'contact'   => 'Contatto',
            ],
            'hero' => [
                'eyebrow'        => 'Akasha Production',
                'title'          => 'Creatività Inter Connessa',
                'lead'           => 'Akasha Production tesse produzioni web di ogni formato — siti concettuali, piattaforme comunitarie, giochi narrativi e opere interattive. Ogni progetto è pensato come una costellazione: intenzioni connesse, un’estetica coerente, un’esperienza che resta.',
                'cta_creations'  => 'Le nostre creazioni',
                'cta_projects'   => 'I tuoi progetti',
            ],
            'featured' => [
                'eyebrow'     => 'In primo piano',
                'title'       => 'Aletheia — The Mystic Quest',
                'description' => "Aletheia è un escape game paradossale online — un’opera viva in cui si entra per curiosità e si scopre, attraversando nove cerchi, che non c’è nulla da cui fuggire: la stanza di cui cerchi l’uscita è presenza. Tre passaggi, venti forme di carte, cinque rituali. Un’esperienza iniziatica distribuibile, pensata come una costellazione narrativa.",
                'image_alt'   => 'Aletheia — The Mystic Quest',
                'cta_label'   => 'Gioca ad Aletheia',
            ],
            'creations' => [
                'eyebrow' => 'Portfolio',
                'title'   => 'Le nostre creazioni',
                'intro'   => 'Una galleria di progetti web consegnati o in orbita — ciascuno con la propria luce.',
                'cards'   => [
                    ['name' => 'Mafiaz.World',           'description' => 'Universo narrativo e sociale attorno a "Ombre & Luci" — gameplay comunitario, economia simbolica e identità grafica distintiva.'],
                    ['name' => 'CMS-Source.org',         'description' => 'CMS modulare e distribuibile, pensato per costruire architetture web fluide e configurabili — la base software delle edizioni Akasha.'],
                    ['name' => 'Permatheque.org',        'description' => 'Biblioteca viva dei saperi della permacultura e dell’autonomia sensibile — organizzata come una foresta commestibile digitale.'],
                    ['name' => 'Conseil-Ayurveda.fr',    'description' => 'Piattaforma di consulenza ayurvedica online — interfaccia distesa, percorso guidato e scrittura editoriale al servizio della cura.'],
                    ['name' => 'Vivre-en-autonomie.fr',  'description' => 'Rivista e quaderno di esperienze sulla vita autonoma — articoli, schede pratiche, sentieri di apprendimento e comunità aperta.'],
                    ['name' => 'Onapprendtouslesjours.fr','description' => 'Spazio pedagogico eclettico — saperi condivisi, micro-formati, esplorazione libera. Un atelier di curiosità quotidiana.'],
                    ['name' => 'Atlas-Access.immo',      'description' => 'Atlante immobiliare marocchino — strumenti per accompagnare investimento, insediamento e valorizzazione del patrimonio.'],
                    ['name' => 'Harmonie-Holistique.org','description' => 'Rete di praticanti e di risorse olistiche — directory, agenda, contenuti editoriali e spazio comunità.'],
                    ['name' => 'Relief.Education',       'description' => 'Piattaforma educativa in orbita — sistema di design narrativo e architettura pedagogica in costruzione.'],
                ],
            ],
            'projects' => [
                'eyebrow'           => 'I tuoi progetti',
                'title'             => 'Una vetrina per le tue creazioni',
                'empty_state'       => 'Nessun progetto è attualmente in evidenza.',
                'intro'             => 'Akasha Production apre uno spazio di promozione per creazioni web innovative.',
                'cms_source_lead'   => 'Cerchi un sostegno per realizzarlo? Visita il nostro',
                'cms_source_link'   => 'spazio creativo su CMS-Source.org',
                'instructions'      => '',
                'form_title'        => 'Unisciti alla costellazione: invia il tuo progetto',
                'submit_label'      => 'Invia il progetto',
                'load_more_label'   => 'Vedi di più',
            ],
            'footer' => [
                'copyright'    => 'Copyright 2026 AkashaProduction.com',
                'legal_link'   => 'Informazioni legali',
                'contact_link' => 'Contattaci',
                'legal_lines'  => [
                    'Robin Prevent',
                    '50 avenue Frédéric Mistral, 83170 Brignoles, Francia',
                    'Telefono: +33 7 81 42 88 71',
                    'Hosting: o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand, Francia',
                ],
            ],
            'contact' => [
                'title'        => 'Contattaci',
                'lead'         => 'Per un progetto, una collaborazione o una semplice domanda — lasciaci un messaggio, rispondiamo in meno di 48 ore.',
                'submit_label' => 'Invia',
            ],
        ],

    ];
}

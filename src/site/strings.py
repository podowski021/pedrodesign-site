L=['pt','en','es','fr','it','de']
def x(*a): return dict(zip(L,a))
T={
'nav_lp':x('Landing pages','Landing pages','Landing pages','Landing pages','Landing page','Landingpages'),
'nav_art':x('Artes','Design','Diseño','Création','Grafica','Design'),
'nav_cta':x('Falar comigo',"Let's talk",'Hablemos','Parlons-en','Parliamone','Kontakt'),
'lang_lbl':x('Idioma','Language','Idioma','Langue','Lingua','Sprache'),
'eyebrow':x('Atendemos o mundo inteiro · todos os idiomas e moedas','We serve the whole world · every language and currency','Atendemos a todo el mundo · todos los idiomas y monedas','Nous servons le monde entier · toutes langues et devises','Serviamo tutto il mondo · ogni lingua e valuta','Wir arbeiten weltweit · in jeder Sprache und Währung'),
'h1a':x('Sites que','Websites that','Sitios que','Des sites qui','Siti che','Websites, die'),
'h1b':x('vendem','sell','venden','vendent','vendono','verkaufen'),
'h1c':x('Feeds que','Feeds that','Feeds que','Des feeds qui','Feed che','Feeds, die'),
'h1d':x('param o scroll','stop the scroll','frenan el scroll','arrêtent le scroll','fermano lo scroll','den Scroll stoppen'),
'hero_p':x('Landing pages, design para Instagram e artes para eventos. Cada projeto feito sob medida, do briefing até o ar.','Landing pages, Instagram design and event graphics. Every project made to measure, from brief to launch.','Landing pages, diseño para Instagram y piezas para eventos. Cada proyecto a medida, del briefing al lanzamiento.',"Landing pages, design Instagram et visuels d'événements. Chaque projet sur mesure, du brief à la mise en ligne.",'Landing page, grafiche per Instagram ed eventi. Ogni progetto su misura, dal brief alla pubblicazione.','Landingpages, Instagram-Design und Event-Grafiken. Jedes Projekt maßgeschneidert, vom Briefing bis zum Launch.'),
'cta_lp':x('Quero uma landing page','I want a landing page','Quiero una landing page','Je veux une landing page','Voglio una landing page','Ich möchte eine Landingpage'),
'cta_art':x('Quero artes','I want designs','Quiero diseños','Je veux des créations','Voglio grafiche','Ich möchte Designs'),
'p_24':x('Entrega em 24h*','24h delivery*','Entrega en 24h*','Livraison en 24h*','Consegna in 24h*','Lieferung in 24h*'),
'p_life':x('Suporte vitalício','Lifetime support','Soporte de por vida','Support à vie','Assistenza a vita','Lebenslanger Support'),
'p_247':x('Suporte 24/7','24/7 support','Soporte 24/7','Support 24/7','Assistenza 24/7','Support rund um die Uhr'),
'p_light':x('Páginas extremamente leves','Ultra-lightweight pages','Páginas ultraligeras','Pages ultra-légères','Pagine leggerissime','Extrem schlanke Seiten'),
'p_world':x('Atendemos o mundo inteiro','We serve the whole world','Atendemos a todo el mundo','Nous servons le monde entier','Serviamo tutto il mondo','Wir arbeiten weltweit'),
'float_order':x('Novo pedido no WhatsApp ✓','New order on WhatsApp ✓','Nuevo pedido en WhatsApp ✓','Nouvelle commande sur WhatsApp ✓','Nuovo ordine su WhatsApp ✓','Neue Anfrage auf WhatsApp ✓'),
'badge':x('FEITO SOB MEDIDA ✦ FEITO SOB MEDIDA ✦ ','MADE TO MEASURE ✦ MADE TO MEASURE ✦ ','HECHO A MEDIDA ✦ HECHO A MEDIDA ✦ ','SUR MESURE ✦ SUR MESURE ✦ SUR MESURE ✦ ','SU MISURA ✦ SU MISURA ✦ SU MISURA ✦ ','MASSARBEIT ✦ MASSARBEIT ✦ MASSARBEIT ✦ '),
'ch1_eb':x('Serviço 01','Service 01','Servicio 01','Service 01','Servizio 01','Leistung 01'),
'ch1_i':x('que vendem por você.','that sell for you.','que venden por ti.','qui vendent pour vous.','che vendono per te.','die für Sie verkaufen.'),
'ch1_p':x('Páginas feitas sob medida para transformar visita em contato, com formulário, WhatsApp e painel para acompanhar cada lead.','Custom pages that turn visits into contacts, with forms, WhatsApp and a dashboard to track every lead.','Páginas a medida que convierten visitas en contactos, con formulario, WhatsApp y panel para seguir cada lead.','Des pages sur mesure qui transforment les visites en contacts, avec formulaire, WhatsApp et tableau de bord pour suivre chaque lead.','Pagine su misura che trasformano le visite in contatti, con modulo, WhatsApp e pannello per seguire ogni lead.','Maßgeschneiderte Seiten, die Besucher zu Kontakten machen – mit Formular, WhatsApp und Dashboard für jeden Lead.'),
'c_leads':x('Captação de leads','Lead generation','Captación de leads','Génération de leads','Acquisizione lead','Lead-Generierung'),
'c_sales':x('Páginas de vendas','Sales pages','Páginas de ventas','Pages de vente','Pagine di vendita','Verkaufsseiten'),
'c_panel':x('Painel de leads','Lead dashboard','Panel de leads','Tableau de leads','Pannello lead','Lead-Dashboard'),
'ch2_eb':x('Serviço 02','Service 02','Servicio 02','Service 02','Servizio 02','Leistung 02'),
'ch2_t':x('Artes & design','Graphics & design','Arte & diseño','Création & design','Grafica & design','Grafik & Design'),
'ch2_i':x('que param o scroll.','that stop the scroll.','que frenan el scroll.','qui arrêtent le scroll.','che fermano lo scroll.','die den Scroll stoppen.'),
'ch2_p':x('Posts, carrosséis, stories e artes para eventos, com a cara da sua marca em cada peça.',"Posts, carousels, stories and event graphics, with your brand's personality in every piece.",'Posts, carruseles, stories y piezas para eventos, con la personalidad de tu marca en cada una.',"Posts, carrousels, stories et visuels d'événements, à l'image de votre marque.",'Post, caroselli, stories e grafiche per eventi, con lo stile del tuo brand in ogni pezzo.','Posts, Karussells, Stories und Event-Grafiken – mit dem Charakter Ihrer Marke in jedem Detail.'),
'c_posts':x('Posts e carrosséis','Posts & carousels','Posts y carruseles','Posts & carrousels','Post e caroselli','Posts & Karussells'),
'c_stories':x('Stories','Stories','Stories','Stories','Stories','Stories'),
'c_events':x('Artes para eventos','Event graphics','Piezas para eventos',"Visuels d'événements",'Grafiche per eventi','Event-Grafiken'),
'c_shirts':x('Camisas e logos','T-shirts & logos','Camisetas y logos','T-shirts & logos','Magliette e loghi','Shirts & Logos'),
'c_banners':x('Banners e apresentações','Banners & presentations','Banners y presentaciones','Bannières & présentations','Banner e presentazioni','Banner & Präsentationen'),
'work_eb':x('Projetos entregues','Delivered projects','Proyectos entregados','Projets livrés','Progetti consegnati','Umgesetzte Projekte'),
'sites_i':x('que convertem','that convert','que convierten','qui convertissent','che convertono','die konvertieren'),
'sites_p':x('Passe o mouse em cada projeto para ver a página inteira rolando.','Hover over each project to watch the whole page scroll.','Pasa el mouse por cada proyecto para ver la página completa.','Survolez chaque projet pour voir la page défiler.','Passa il mouse su ogni progetto per vedere la pagina scorrere.','Fahren Sie über ein Projekt, um die ganze Seite zu sehen.'),
'gni_desc':x('Landing de intercâmbio para brasileiros que querem morar na Irlanda, com painel de vendas para a equipe.','Study-abroad landing page for Brazilians moving to Ireland, with a sales dashboard for the team.','Landing de intercambio para brasileños que quieren vivir en Irlanda, con panel de ventas para el equipo.',"Landing page d'études à l'étranger pour Brésiliens partant vivre en Irlande, avec tableau de ventes pour l'équipe.",'Landing di studio all\'estero per brasiliani che vogliono vivere in Irlanda, con pannello vendite per il team.','Landingpage für Auslandsaufenthalte von Brasilianern in Irland – mit Vertriebs-Dashboard fürs Team.'),
'see_case':x('Ver o case completo','See the full case study','Ver el caso completo',"Voir l'étude de cas",'Vedi il case study','Zur Fallstudie'),
'see_live':x('Ver site no ar','View live site','Ver sitio en vivo','Voir le site en ligne','Vedi il sito online','Live-Seite ansehen'),
'case_eb':x('Case completo','Case study','Caso completo','Étude de cas','Case study','Fallstudie'),
'case_i':x('do zero ao ar','from zero to live','de cero al lanzamiento','de zéro à la mise en ligne','da zero alla pubblicazione','von null bis live'),
'close':x('Fechar','Close','Cerrar','Fermer','Chiudi','Schließen'),
'painel_note':x('Acesso exclusivo da equipe de vendas. Telas internas:','Sales team access only. Internal screens:','Acceso exclusivo del equipo de ventas. Pantallas internas:',"Accès réservé à l'équipe commerciale. Écrans internes :",'Accesso riservato al team vendite. Schermate interne:','Nur für das Vertriebsteam. Interne Ansichten:'),
'invest':x('Investimento médio','Average investment','Inversión media','Investissement moyen','Investimento medio','Durchschnittliche Investition'),
'invest_sub':x('Landing + painel de vendas','Landing page + sales dashboard','Landing + panel de ventas','Landing page + tableau de ventes','Landing + pannello vendite','Landingpage + Vertriebs-Dashboard'),
'scroll_hint':x('A página desce sozinha. Role com o mouse ou o dedo para explorar no seu ritmo.','The page scrolls on its own. Use your mouse or finger to explore at your own pace.','La página baja sola. Desplázate con el mouse o el dedo a tu ritmo.','La page défile toute seule. Faites défiler à la souris ou au doigt, à votre rythme.','La pagina scorre da sola. Scorri con il mouse o il dito al tuo ritmo.','Die Seite scrollt von selbst. Mit Maus oder Finger erkunden Sie sie in Ihrem Tempo.'),
'gar_eb':x('Garantias de toda landing page','Guarantees on every landing page','Garantías en cada landing page','Garanties sur chaque landing page','Garanzie su ogni landing page','Garantien für jede Landingpage'),
'gar_h':x('Compromisso','A commitment','Compromiso','Un engagement','Un impegno','Ein Versprechen'),
'gar_i':x('por escrito','in writing','por escrito','écrit','per iscritto','schriftlich'),
'gar_p':x('Você contrata com segurança, do primeiro dia até muito depois da entrega.','Hire with confidence, from day one to long after delivery.','Contratas con seguridad, desde el primer día hasta mucho después de la entrega.','Engagez-vous en toute sérénité, du premier jour à bien après la livraison.','Scegli in tutta sicurezza, dal primo giorno a molto dopo la consegna.','Sicher beauftragen – vom ersten Tag bis lange nach der Übergabe.'),
'g1_t':x('Sua página entregue em 24h','Your page delivered in 24h','Tu página entregada en 24h','Votre page livrée en 24h','La tua pagina consegnata in 24h','Ihre Seite in 24h geliefert'),
'g1_p':x('Ou devolvemos o seu dinheiro.*','Or your money back.*','O te devolvemos tu dinero.*','Ou nous vous remboursons.*','O ti restituiamo i soldi.*','Oder Sie erhalten Ihr Geld zurück.*'),
'g1_l':x('*Consultar regulamento','*Terms apply','*Consulta el reglamento','*Voir conditions','*Consulta il regolamento','*Es gelten Bedingungen'),
'g2_p':x('Sua página nunca fica sozinha. Precisou, é só chamar.','Your page is never on its own. Need anything? Just call.','Tu página nunca queda sola. ¿Necesitas algo? Solo escribe.',"Votre page n'est jamais seule. Un besoin ? Il suffit d'écrire.",'La tua pagina non resta mai sola. Serve qualcosa? Basta scrivere.','Ihre Seite ist nie allein. Brauchen Sie etwas? Einfach melden.'),
'g3_t':x('Suporte 24 horas, 7 dias por semana','Support 24 hours, 7 days a week','Soporte 24 horas, 7 días a la semana','Support 24h/24, 7j/7','Assistenza 24 ore su 24, 7 giorni su 7','Support 24 Stunden, 7 Tage die Woche'),
'g3_p':x('Madrugada, fim de semana ou feriado: tem gente do outro lado.',"Late night, weekend or holiday: there's someone on the other side.",'Madrugada, fin de semana o feriado: hay alguien del otro lado.',"La nuit, le week-end ou les jours fériés : quelqu'un vous répond.","Notte, weekend o festivi: c'è sempre qualcuno dall'altra parte.",'Nachts, am Wochenende oder an Feiertagen: Es ist immer jemand da.'),
'g4_big':x('Leve','Light','Ligera','Léger','Leggera','Leicht'),
'g4_p':x('Abrem rápido no celular, para o cliente não desistir antes de ver.',"They load fast on mobile, so visitors don't leave before they see.",'Cargan rápido en el móvil, para que nadie se vaya antes de ver.',"Elles s'ouvrent vite sur mobile, pour que personne ne parte avant d'avoir vu.",'Si aprono veloci sul cellulare, così nessuno se ne va prima di vedere.','Sie laden schnell auf dem Handy, damit niemand vorher abspringt.'),
'g5_p':x('Em qualquer idioma, recebendo em qualquer moeda.','In any language, with payment in any currency.','En cualquier idioma, cobrando en cualquier moneda.','Dans toutes les langues, paiement dans toutes les devises.','In qualsiasi lingua, con pagamento in qualsiasi valuta.','In jeder Sprache, Zahlung in jeder Währung.'),
'insta_h':x('Feeds','Feeds','Feeds','Des feeds','Feed','Feeds'),
'insta_i':x('com assinatura','with a signature','con firma propia','signés',"d'autore",'mit Handschrift'),
'insta_p':x('Escolha um cliente e veja o feed ganhar vida.','Pick a client and watch the feed come alive.','Elige un cliente y mira el feed cobrar vida.','Choisissez un client et voyez le feed prendre vie.','Scegli un cliente e guarda il feed prendere vita.','Wählen Sie einen Kunden und sehen Sie den Feed lebendig werden.'),
'feed_cta':x('Quero um feed assim','I want a feed like this','Quiero un feed así','Je veux un feed comme ça','Voglio un feed così','So einen Feed möchte ich'),
'posts_n':x('[nº] publicações','[no.] posts','[nº] publicaciones','[nº] publications','[n.] post','[Anz.] Beiträge'),
'ct_eb':x('Contato','Contact','Contacto','Contact','Contatti','Kontakt'),
'ct_a':x('Bora tirar',"Let's bring",'Saquemos','Donnons vie','Diamo vita','Lassen Sie uns'),
'ct_b':x('sua ideia','your idea','tu idea','à votre idée','alla tua idea','Ihre Idee'),
'ct_c':x('do papel?','to life.','del papel.','.','.','umsetzen.'),
'ct_p':x('Me conta rapidinho o que você precisa. A conversa continua no WhatsApp.',"Tell me briefly what you need. We'll continue on WhatsApp.",'Cuéntame rápido lo que necesitas. Seguimos por WhatsApp.',"Dites-moi en deux mots ce dont vous avez besoin. On continue sur WhatsApp.",'Raccontami in breve cosa ti serve. Continuiamo su WhatsApp.','Sagen Sie mir kurz, was Sie brauchen. Wir sprechen auf WhatsApp weiter.'),
'ct_direct':x('Prefere chamar direto?','Prefer to message directly?','¿Prefieres escribir directo?','Vous préférez écrire directement ?','Preferisci scrivere direttamente?','Lieber direkt schreiben?'),
'f_name':x('Seu nome','Your name','Tu nombre','Votre nom','Il tuo nome','Ihr Name'),
'f_name_ph':x('Como posso te chamar?','What should I call you?','¿Cómo te llamo?','Comment vous appeler ?','Come ti chiami?','Wie darf ich Sie nennen?'),
'f_zap_ph':x('+55 45 99999-9999','+1 555 123 4567','+34 600 000 000','+33 6 00 00 00 00','+39 300 000 0000','+49 151 00000000'),
'f_need':x('O que você precisa?','What do you need?','¿Qué necesitas?','De quoi avez-vous besoin ?','Di cosa hai bisogno?','Was brauchen Sie?'),
'svc_lp':x('Landing page','Landing page','Landing page','Landing page','Landing page','Landingpage'),
'svc_art':x('Artes','Designs','Diseños','Créations','Grafiche','Designs'),
'f_msg':x('Em poucas palavras','In a few words','En pocas palabras','En quelques mots','In poche parole','In wenigen Worten'),
'f_msg_ph':x('Ex.: landing para captar clientes da minha clínica','E.g. a landing page to get clients for my clinic','Ej.: landing para captar clientes de mi clínica','Ex. : une landing page pour attirer des clients à mon cabinet','Es.: landing per trovare clienti per la mia clinica','Z. B. Landingpage für neue Kunden meiner Praxis'),
'f_btn':x('Chamar no WhatsApp','Message on WhatsApp','Escribir por WhatsApp','Écrire sur WhatsApp','Scrivi su WhatsApp','Auf WhatsApp schreiben'),
'foot_a':x('Vamos criar',"Let's create",'Creemos','Créons','Creiamo','Lassen Sie uns'),
'foot_b':x('algo seu?','something yours?','algo tuyo?','quelque chose à vous ?','qualcosa di tuo?','etwas Eigenes schaffen?'),
'foot_by':x('Site desenvolvido por Pedro','Website by Pedro','Sitio desarrollado por Pedro','Site réalisé par Pedro','Sito realizzato da Pedro','Website von Pedro'),
'foot_btn':x('Orçamento no WhatsApp','Get a quote on WhatsApp','Presupuesto por WhatsApp','Devis sur WhatsApp','Preventivo su WhatsApp','Angebot per WhatsApp'),
'wa_lp':x('Olá, Pedro! Tenho interesse em uma landing page.',"Hi Pedro! I'm interested in a landing page.",'¡Hola, Pedro! Me interesa una landing page.','Bonjour Pedro ! Je suis intéressé(e) par une landing page.','Ciao Pedro! Mi interessa una landing page.','Hallo Pedro! Ich interessiere mich für eine Landingpage.'),
'wa_art':x('Olá, Pedro! Tenho interesse em artes.',"Hi Pedro! I'm interested in designs.",'¡Hola, Pedro! Me interesan diseños.','Bonjour Pedro ! Je suis intéressé(e) par des créations.','Ciao Pedro! Mi interessano delle grafiche.','Hallo Pedro! Ich interessiere mich für Designs.'),
'wa_name':x('Nome','Name','Nombre','Nom','Nome','Name'),
'wa_about':x('Sobre o projeto','About the project','Sobre el proyecto','Le projet','Il progetto','Zum Projekt'),
}
# arrays
A={
'tabs':{'pt':['Computador','Celular','Painel de vendas'],'en':['Desktop','Mobile','Sales dashboard'],'es':['Escritorio','Móvil','Panel de ventas'],'fr':['Ordinateur','Mobile','Tableau de ventes'],'it':['Desktop','Mobile','Pannello vendite'],'de':['Desktop','Mobil','Vertriebs-Dashboard']},
'listTitles':{'pt':['Na versão de computador','Na versão de celular','No painel de vendas'],'en':['On desktop','On mobile','In the sales dashboard'],'es':['En la versión de escritorio','En la versión móvil','En el panel de ventas'],'fr':['Sur ordinateur','Sur mobile','Dans le tableau de ventes'],'it':['Nella versione desktop','Nella versione mobile','Nel pannello vendite'],'de':['In der Desktop-Version','In der mobilen Version','Im Vertriebs-Dashboard']},
'lists':{
'pt':[['Mapa interativo da Irlanda com as cidades','Formulário logo no topo que leva direto ao WhatsApp','Depoimentos em áudio e prints de alunos','Perguntas frequentes','Visual com a cara da Irlanda, em tom descontraído'],['Feita primeiro para o celular','Formulário fácil de preencher com o polegar','Botões grandes e menu sempre à mão'],['Leads chegam direto no painel','Temperatura automática: quente, morno e frio','Funil de status para a equipe de vendas','Relatórios em Excel','Aviso por e-mail a cada lead novo','Lixeira com limpeza automática em 30 dias']],
'en':[['Interactive map of Ireland with its cities','Form right at the top that leads straight to WhatsApp','Student audio testimonials and screenshots','FAQ','An Irish look with a relaxed tone'],['Designed mobile-first','Form easy to fill in with your thumb','Big buttons and a menu always at hand'],['Leads arrive straight in the dashboard','Automatic temperature: hot, warm and cold','Status pipeline for the sales team','Excel reports','Email alert for every new lead','Trash with automatic 30-day cleanup']],
'es':[['Mapa interactivo de Irlanda con sus ciudades','Formulario arriba que lleva directo a WhatsApp','Testimonios en audio y capturas de alumnos','Preguntas frecuentes','Estética irlandesa con tono relajado'],['Diseñada primero para el móvil','Formulario fácil de llenar con el pulgar','Botones grandes y menú siempre a mano'],['Los leads llegan directo al panel','Temperatura automática: caliente, tibio y frío','Embudo de estados para el equipo de ventas','Informes en Excel','Aviso por e-mail con cada lead nuevo','Papelera con limpieza automática en 30 días']],
'fr':[["Carte interactive de l'Irlande et de ses villes",'Formulaire en haut qui mène directement à WhatsApp',"Témoignages audio et captures d'élèves",'FAQ','Un look irlandais au ton décontracté'],["Pensée d'abord pour le mobile",'Formulaire facile à remplir au pouce','Grands boutons et menu toujours à portée'],['Les leads arrivent directement dans le tableau','Température automatique : chaud, tiède, froid',"Pipeline de statuts pour l'équipe commerciale",'Rapports Excel','Alerte e-mail à chaque nouveau lead','Corbeille vidée automatiquement après 30 jours']],
'it':[["Mappa interattiva dell'Irlanda con le città",'Modulo in alto che porta dritto a WhatsApp','Testimonianze audio e screenshot degli studenti','Domande frequenti','Look irlandese dal tono informale'],['Progettata prima per il mobile','Modulo facile da compilare con il pollice','Pulsanti grandi e menu sempre a portata'],['I lead arrivano dritti nel pannello','Temperatura automatica: caldo, tiepido e freddo','Funnel di stato per il team vendite','Report in Excel','Avviso via e-mail per ogni nuovo lead','Cestino con pulizia automatica a 30 giorni']],
'de':[['Interaktive Irland-Karte mit Städten','Formular ganz oben, direkt zu WhatsApp','Audio-Feedback und Screenshots von Schülern','FAQ','Irischer Look mit lockerem Ton'],['Mobile-first gestaltet','Formular bequem mit dem Daumen ausfüllbar','Große Buttons, Menü immer griffbereit'],['Leads landen direkt im Dashboard','Automatische Einstufung: heiß, warm, kalt','Status-Pipeline fürs Vertriebsteam','Excel-Berichte','E-Mail bei jedem neuen Lead','Papierkorb mit automatischer Leerung nach 30 Tagen']]},
'clients':{
'pt':[('Feed + apresentação','Posts de feed e apresentação para a marca pet, com referências adaptadas ao estilo do cliente.'),('Feed','Posts semanais para o perfil de decoração: produtos, ambientes e inspiração.'),('Evento esportivo','Artes da prova noturna: posts de divulgação, camisa e logo da edição.'),('Evento','Banner e apresentação do evento, com a mesma linguagem em todas as peças.'),('Prova','Peças para a prova do último sobrevivente, incluindo a arte do troféu único.')],
'en':[('Feed + presentation',"Feed posts and a presentation for the pet brand, with references adapted to the client's style."),('Feed','Weekly posts for the home decor profile: products, rooms and inspiration.'),('Sports event','Graphics for the night race: promo posts, race shirt and edition logo.'),('Event','Event banner and presentation, with one consistent look across every piece.'),('Race','Pieces for the "last survivor" race, including the one-of-a-kind trophy art.')],
'es':[('Feed + presentación','Posts de feed y presentación para la marca de mascotas, con referencias adaptadas al estilo del cliente.'),('Feed','Posts semanales para el perfil de decoración: productos, ambientes e inspiración.'),('Evento deportivo','Piezas de la carrera nocturna: posts de difusión, camiseta y logo de la edición.'),('Evento','Banner y presentación del evento, con el mismo lenguaje en todas las piezas.'),('Carrera','Piezas para la carrera del último sobreviviente, incluido el arte del trofeo único.')],
'fr':[('Feed + présentation','Posts et présentation pour la marque pour animaux, avec des références adaptées au style du client.'),('Feed','Posts hebdomadaires pour le profil déco : produits, intérieurs et inspiration.'),('Événement sportif',"Visuels de la course nocturne : posts de promo, t-shirt et logo de l'édition."),('Événement',"Bannière et présentation de l'événement, avec le même langage visuel partout."),('Course','Visuels pour la course du dernier survivant, dont le trophée unique.')],
'it':[('Feed + presentazione','Post e presentazione per il brand pet, con riferimenti adattati allo stile del cliente.'),('Feed','Post settimanali per il profilo di arredamento: prodotti, ambienti e ispirazione.'),('Evento sportivo',"Grafiche della gara notturna: post promozionali, maglietta e logo dell'edizione."),('Evento',"Banner e presentazione dell'evento, con lo stesso linguaggio in ogni pezzo."),('Gara',"Grafiche per la gara dell'ultimo sopravvissuto, incluso il trofeo unico.")],
'de':[('Feed + Präsentation','Feed-Posts und Präsentation für die Tiermarke, mit Referenzen im Stil des Kunden.'),('Feed','Wöchentliche Posts für das Deko-Profil: Produkte, Räume und Inspiration.'),('Sportevent','Grafiken für den Nachtlauf: Promo-Posts, Laufshirt und Logo der Ausgabe.'),('Event','Banner und Präsentation des Events, in einheitlicher Gestaltung.'),('Rennen','Grafiken für das „Last Survivor“-Rennen, inklusive der einzigartigen Trophäe.')]},
'vocab':{
'pt':{'Post':'Post','Carrossel':'Carrossel','Story':'Story','Apresentação':'Apresentação','Logo da edição':'Logo da edição','Camisa':'Camisa','Kit':'Kit','Banner':'Banner','Troféu':'Troféu'},
'en':{'Post':'Post','Carrossel':'Carousel','Story':'Story','Apresentação':'Presentation','Logo da edição':'Edition logo','Camisa':'Shirt','Kit':'Kit','Banner':'Banner','Troféu':'Trophy'},
'es':{'Post':'Post','Carrossel':'Carrusel','Story':'Story','Apresentação':'Presentación','Logo da edição':'Logo de la edición','Camisa':'Camiseta','Kit':'Kit','Banner':'Banner','Troféu':'Trofeo'},
'fr':{'Post':'Post','Carrossel':'Carrousel','Story':'Story','Apresentação':'Présentation','Logo da edição':"Logo de l'édition",'Camisa':'T-shirt','Kit':'Kit','Banner':'Bannière','Troféu':'Trophée'},
'it':{'Post':'Post','Carrossel':'Carosello','Story':'Story','Apresentação':'Presentazione','Logo da edição':"Logo dell'edizione",'Camisa':'Maglietta','Kit':'Kit','Banner':'Banner','Troféu':'Trofeo'},
'de':{'Post':'Post','Carrossel':'Karussell','Story':'Story','Apresentação':'Präsentation','Logo da edição':'Logo der Ausgabe','Camisa':'Shirt','Kit':'Set','Banner':'Banner','Troféu':'Trophäe'}},
}
T.update({
'how_eb':x('Como funciona','How it works','Cómo funciona','Comment ça marche','Come funziona',"So funktioniert's"),
'how_h':x('Do primeiro oi','From the first hello','Del primer hola','Du premier bonjour','Dal primo ciao','Vom ersten Hallo'),
'how_i':x('até muito depois do ar.','to long after launch.','hasta mucho después del lanzamiento.','à bien après la mise en ligne.','a molto dopo la pubblicazione.','bis lange nach dem Launch.'),
'how_p':x('Um processo simples, com tudo combinado por escrito e você por dentro de cada etapa.','A simple process, everything agreed in writing, with you in the loop at every step.','Un proceso simple, todo acordado por escrito y tú al tanto de cada etapa.','Un processus simple, tout convenu par écrit, et vous informé à chaque étape.','Un processo semplice, tutto concordato per iscritto e tu aggiornato a ogni fase.','Ein einfacher Ablauf, alles schriftlich vereinbart, und Sie sind bei jedem Schritt dabei.'),
's1_t':x('Conversa','First chat','Conversación','Premier échange','Prima chiacchierata','Erstgespräch'),
's1_p':x('Você conta o que precisa pelo WhatsApp, no seu idioma.','You tell us what you need on WhatsApp, in your language.','Nos cuentas lo que necesitas por WhatsApp, en tu idioma.',"Vous nous dites ce dont vous avez besoin sur WhatsApp, dans votre langue.",'Ci racconti cosa ti serve su WhatsApp, nella tua lingua.','Sie schildern uns Ihr Anliegen per WhatsApp, in Ihrer Sprache.'),
's2_t':x('Proposta transparente','Transparent proposal','Propuesta transparente','Proposition transparente','Proposta trasparente','Transparentes Angebot'),
's2_p':x('Preço, prazo e tudo o que está incluso, por escrito. Sem letras miúdas.','Price, timeline and everything included, in writing. No fine print.','Precio, plazo y todo lo incluido, por escrito. Sin letra pequeña.','Prix, délais et tout ce qui est inclus, par écrit. Sans petites lignes.','Prezzo, tempi e tutto ciò che è incluso, per iscritto. Senza clausole nascoste.','Preis, Zeitplan und alle Leistungen schriftlich. Ohne Kleingedrucktes.'),
's3_t':x('Criação e aprovação','Design & approval','Creación y aprobación','Création et validation','Creazione e approvazione','Gestaltung & Freigabe'),
's3_p':x('Você acompanha o projeto e aprova cada etapa antes de seguir.','You follow the project and approve each step before we move on.','Sigues el proyecto y apruebas cada etapa antes de avanzar.','Vous suivez le projet et validez chaque étape avant la suite.','Segui il progetto e approvi ogni fase prima di proseguire.','Sie verfolgen das Projekt und geben jeden Schritt frei.'),
's4_t':x('Entrega completa','Complete delivery','Entrega completa','Livraison complète','Consegna completa','Komplette Übergabe'),
's4_p':x('Cuidamos do domínio e da hospedagem e entregamos tutoriais para você usar tudo com autonomia.','We handle the domain and hosting and give you tutorials to use everything on your own.','Nos encargamos del dominio y el hosting y te damos tutoriales para usarlo todo con autonomía.',"Nous gérons le domaine et l'hébergement et vous fournissons des tutoriels pour être autonome.",'Ci occupiamo di dominio e hosting e ti diamo tutorial per usare tutto in autonomia.','Wir kümmern uns um Domain und Hosting und liefern Tutorials, damit Sie alles selbst nutzen können.'),
's5_t':x('Suporte contínuo','Ongoing support','Soporte continuo','Support continu','Assistenza continua','Laufender Support'),
's5_p':x('Depois da entrega, seguimos com você. Precisou, é só chamar.','After delivery we stay by your side. Need anything? Just call.','Después de la entrega seguimos contigo. ¿Necesitas algo? Solo escribe.',"Après la livraison, on reste à vos côtés. Un besoin ? Il suffit d'écrire.",'Dopo la consegna restiamo al tuo fianco. Serve qualcosa? Basta scrivere.','Nach der Übergabe bleiben wir an Ihrer Seite. Einfach melden.'),
'tr_clear_t':x('Tudo 100% claro','100% clear, always','Todo 100% claro','Tout est 100 % clair','Tutto chiaro al 100%','Alles 100 % klar'),
'tr_clear_p':x('Escopo, valores e prazos documentados. Combinado é combinado.','Scope, pricing and deadlines documented. A deal is a deal.','Alcance, valores y plazos documentados. Lo acordado se cumple.','Périmètre, tarifs et délais documentés. Parole tenue.','Ambito, costi e tempi documentati. I patti sono patti.','Umfang, Preise und Fristen dokumentiert. Abgemacht ist abgemacht.'),
'tr_nf_t':x('Emitimos nota fiscal','We issue official invoices','Emitimos factura oficial','Nous émettons des factures','Emettiamo fattura','Wir stellen offizielle Rechnungen aus'),
'tr_nf_p':x('Todo projeto sai com nota fiscal em nome da empresa.','Every project comes with an official invoice from our company.','Cada proyecto incluye factura oficial de nuestra empresa.','Chaque projet est facturé officiellement par notre entreprise.',"Ogni progetto è accompagnato da fattura ufficiale della nostra azienda.",'Jedes Projekt wird offiziell von unserem Unternehmen abgerechnet.'),
'tr_cnpj_p':x('Empresa registrada no Brasil.','Company registered in Brazil.','Empresa registrada en Brasil.','Entreprise enregistrée au Brésil.','Azienda registrata in Brasile.','In Brasilien registriertes Unternehmen.'),
'tr_sup_t':x('Suporte do início ao fim','Support from start to finish','Soporte de principio a fin','Accompagnement du début à la fin',"Supporto dall'inizio alla fine",'Support von Anfang bis Ende'),
'c_host':x('Hospedagem','Hosting','Hosting','Hébergement','Hosting','Hosting'),
'c_domain':x('Domínio','Domain','Dominio','Domaine','Dominio','Domain'),
'c_tut':x('Tutoriais','Tutorials','Tutoriales','Tutoriels','Tutorial','Tutorials'),
})
T.update({
'h1a':x('Sua landing page','Your landing page','Tu landing page','Votre landing page','La tua landing page','Ihre Landingpage'),
'h1b':x('entregue em 24h','delivered in 24h','entregada en 24h','livrée en 24h','consegnata in 24h','in 24h geliefert'),
'h1d':x('ou seu dinheiro de volta.','or your money back.','o te devolvemos el dinero.','ou remboursée.','o ti rimborsiamo.','oder Geld zurück.'),
'hero_p':x('Páginas sob medida, leves e prontas para vender, com suporte vitalício. Também criamos artes para Instagram e eventos.','Custom, lightweight pages built to sell, with lifetime support. We also create Instagram and event graphics.','Páginas a medida, ligeras y listas para vender, con soporte de por vida. También creamos diseños para Instagram y eventos.','Des pages sur mesure, légères et prêtes à vendre, avec support à vie. Nous créons aussi des visuels pour Instagram et événements.','Pagine su misura, leggere e pronte a vendere, con assistenza a vita. Creiamo anche grafiche per Instagram ed eventi.','Maßgeschneiderte, schlanke Seiten, die verkaufen – mit lebenslangem Support. Außerdem gestalten wir Instagram- und Event-Grafiken.'),
'all':x('Todos','All','Todos','Tous','Tutti','Alle'),
'gal_p':x('Passe o mouse para pausar. Escolha um cliente para ver só as artes dele.','Hover to pause. Pick a client to see only their work.','Pasa el mouse para pausar. Elige un cliente para ver solo sus diseños.','Survolez pour mettre en pause. Choisissez un client pour voir ses créations.','Passa il mouse per mettere in pausa. Scegli un cliente per vedere solo le sue grafiche.','Zum Pausieren darüberfahren. Wählen Sie einen Kunden, um nur seine Arbeiten zu sehen.'),
'gar_feat':x('Sua landing page entregue em 24h ou devolvemos o seu dinheiro.*','Your landing page delivered in 24h or your money back.*','Tu landing page entregada en 24h o te devolvemos el dinero.*','Votre landing page livrée en 24h ou remboursée.*','La tua landing page consegnata in 24h o ti rimborsiamo.*','Ihre Landingpage in 24h geliefert – oder Geld zurück.*'),
})
T.update({
'reg_t':x('Regulamento da garantia de 24h','24h guarantee terms','Reglamento de la garantía de 24h','Conditions de la garantie 24h','Regolamento della garanzia 24h','Bedingungen der 24h-Garantie'),
'reg_1t':x('Quando o prazo começa','When the clock starts','Cuándo empieza el plazo','Quand le délai commence','Quando inizia il termine','Wann die Frist beginnt'),
'reg_1p':x('O prazo de 24h começa a contar a partir da confirmação do pagamento.','The 24h deadline starts counting once payment is confirmed.','El plazo de 24h empieza a contar desde la confirmación del pago.','Le délai de 24h commence à la confirmation du paiement.','Il termine di 24h decorre dalla conferma del pagamento.','Die 24h-Frist beginnt mit der Bestätigung der Zahlung.'),
'reg_2t':x('Pagamento','Payment','Pago','Paiement','Pagamento','Zahlung'),
'reg_2p':x('O pagamento é de 100% antes do início do serviço.','Payment is 100% upfront, before work begins.','El pago es del 100% antes de iniciar el servicio.','Le paiement est de 100 % avant le début du service.',"Il pagamento è del 100% prima dell'inizio del servizio.",'Die Zahlung erfolgt zu 100 % vor Beginn der Arbeit.'),
'reg_3t':x('Parcelamento','Installments','Cuotas','Paiement en plusieurs fois','Rate','Ratenzahlung'),
'reg_3p':x('Em até 6x sem juros ou em até 10x com juros.','Up to 6 interest-free installments, or up to 10 with interest.','Hasta 6 cuotas sin intereses o hasta 10 con intereses.',"Jusqu'à 6 fois sans frais ou jusqu'à 10 fois avec frais.",'Fino a 6 rate senza interessi o fino a 10 con interessi.','Bis zu 6 zinsfreie Raten oder bis zu 10 Raten mit Zinsen.'),
'reg_ok':x('Entendi','Got it','Entendido','Compris','Ho capito','Verstanden'),
})
T.update({
'foot_rights':x('Todos os direitos reservados.','All rights reserved.','Todos los derechos reservados.','Tous droits réservés.','Tutti i diritti riservati.','Alle Rechte vorbehalten.'),
'foot_co':x('Empresa','Company','Empresa','Entreprise','Azienda','Unternehmen'),
'foot_nav':x('Navegação','Navigation','Navegación','Navigation','Navigazione','Navigation'),
'reg_link':x('Regulamento','Terms','Reglamento','Conditions','Regolamento','Bedingungen'),
})
T.update({'feed_cta':x('Quero artes assim','I want designs like these','Quiero diseños así','Je veux des créations comme ça','Voglio grafiche così','Solche Designs möchte ich')})
T.update({'gal_p':x('Escolha um cliente para ver as artes dele. Passe o mouse para pausar.','Pick a client to see their work. Hover to pause.','Elige un cliente para ver sus diseños. Pasa el mouse para pausar.','Choisissez un client pour voir ses créations. Survolez pour mettre en pause.','Scegli un cliente per vedere le sue grafiche. Passa il mouse per mettere in pausa.','Wählen Sie einen Kunden, um seine Arbeiten zu sehen. Zum Pausieren darüberfahren.')})
T.update({
'tr_cnpj_p':x('Empresa ativa na Receita Federal · Rio de Janeiro, Brasil.','Active company registered in Rio de Janeiro, Brazil.','Empresa activa registrada en Río de Janeiro, Brasil.','Entreprise active enregistrée à Rio de Janeiro, Brésil.','Azienda attiva registrata a Rio de Janeiro, Brasile.','Aktives Unternehmen, registriert in Rio de Janeiro, Brasilien.'),
'prev':x('Anterior','Previous','Anterior','Précédent','Precedente','Zurück'),
'next':x('Próxima','Next','Siguiente','Suivant','Successiva','Weiter'),
'see_art':x('Ver arte','View design','Ver diseño','Voir la création','Vedi la grafica','Design ansehen'),
})
T.update({'clients_eb':x('Clientes atendidos','Clients we have worked with','Clientes atendidos','Ils nous ont fait confiance','Clienti serviti','Kunden, für die wir arbeiten')})
T.update({
'lp_f1':x('Mapa interativo da Irlanda','Interactive map of Ireland','Mapa interactivo de Irlanda',"Carte interactive de l'Irlande",'Mappa interattiva dell\'Irlanda','Interaktive Irland-Karte'),
'lp_f2':x('Formulário que cai direto no WhatsApp','Form that lands straight on WhatsApp','Formulario que llega directo a WhatsApp','Formulaire qui arrive directement sur WhatsApp','Modulo che arriva dritto su WhatsApp','Formular direkt zu WhatsApp'),
'lp_f3':x('Painel de vendas com cada lead','Sales dashboard with every lead','Panel de ventas con cada lead','Tableau de ventes avec chaque lead','Pannello vendite con ogni lead','Vertriebs-Dashboard mit jedem Lead'),
'see_painel':x('Ver painel de vendas','View sales dashboard','Ver panel de ventas','Voir le tableau de ventes','Vedi il pannello vendite','Vertriebs-Dashboard ansehen'),
'live':x('No ar','Live','En vivo','En ligne','Online','Live'),
'toast':x('Novo lead no painel','New lead in the dashboard','Nuevo lead en el panel','Nouveau lead dans le tableau','Nuovo lead nel pannello','Neuer Lead im Dashboard'),
'next_t':x('Seu projeto pode ser o próximo.','Your project could be next.','Tu proyecto puede ser el próximo.','Votre projet pourrait être le prochain.','Il tuo progetto può essere il prossimo.','Ihr Projekt könnte das nächste sein.'),
'next_p':x('Landing page sob medida, entregue em 24h*.','Custom landing page, delivered in 24h*.','Landing page a medida, entregada en 24h*.','Landing page sur mesure, livrée en 24h*.','Landing page su misura, consegnata in 24h*.','Maßgeschneiderte Landingpage, in 24h* geliefert.'),
'scroll_hint':x('Role dentro das telas para explorar.','Scroll inside the screens to explore.','Desplázate dentro de las pantallas para explorar.','Faites défiler dans les écrans pour explorer.','Scorri dentro gli schermi per esplorare.','Scrollen Sie in den Bildschirmen, um sie zu erkunden.'),
})
T.update({
'fb_eb':x('Feedbacks','Feedback','Opiniones','Avis','Feedback','Feedback'),
'fb_h':x('Quem contratou,','Clients who hired us','Quien nos contrató','Ceux qui nous ont choisis','Chi ci ha scelto','Wer uns beauftragt,'),
'fb_i':x('recomenda.','recommend us.','nos recomienda.','nous recommandent.','ci consiglia.','empfiehlt uns.'),
'fb_p':x('Prints e áudios reais de quem já trabalhou com a gente.','Real screenshots and voice notes from people we have worked with.','Capturas y audios reales de quienes ya trabajaron con nosotros.','De vraies captures et notes vocales de nos clients.','Screenshot e audio reali di chi ha già lavorato con noi.','Echte Screenshots und Sprachnachrichten unserer Kunden.'),
'fb_play':x('Ouvir áudio','Play voice note','Escuchar audio',"Écouter l'audio","Ascolta l'audio",'Sprachnachricht abspielen'),
'fb_pause':x('Pausar','Pause','Pausar','Pause','Pausa','Pause'),
})
T.update({
'tr_ct_t':x('Tudo em contrato','Everything under contract','Todo por contrato','Tout est contractuel','Tutto a contratto','Alles vertraglich geregelt'),
'tr_ct_p':x('Contrato de prestação de serviço assinado: garantia e segurança para os dois lados.','A signed service contract: guarantees and security for both sides.','Contrato de servicio firmado: garantía y seguridad para ambas partes.','Un contrat de prestation signé : garanties et sécurité pour les deux parties.','Contratto di servizio firmato: garanzia e sicurezza per entrambe le parti.','Unterschriebener Dienstleistungsvertrag: Garantie und Sicherheit für beide Seiten.'),
's2_p':x('Preço, prazo e tudo o que está incluso, em contrato. Sem letras miúdas.','Price, timeline and everything included, under contract. No fine print.','Precio, plazo y todo lo incluido, por contrato. Sin letra pequeña.','Prix, délais et tout ce qui est inclus, par contrat. Sans petites lignes.','Prezzo, tempi e tutto ciò che è incluso, a contratto. Senza clausole nascoste.','Preis, Zeitplan und alle Leistungen per Vertrag. Ohne Kleingedrucktes.'),
})
T.update({
'st_t':x('Landing page entregue em 24h*','Landing page delivered in 24h*','Landing page entregada en 24h*','Landing page livrée en 24h*','Landing page consegnata in 24h*','Landingpage in 24h* geliefert'),
'st_form':x('Preencher formulário','Fill in the form','Completar formulario','Remplir le formulaire','Compila il modulo','Formular ausfüllen'),
})
T.update({
'more_pj':x('Mais projetos','More projects','Más proyectos','Autres projets','Altri progetti','Weitere Projekte'),
'your_pj':x('Seu projeto aqui','Your project here','Tu proyecto aquí','Votre projet ici','Il tuo progetto qui','Ihr Projekt hier'),
'prev_pj':x('Projetos anteriores','Previous projects','Proyectos anteriores','Projets précédents','Progetti precedenti','Vorherige Projekte'),
'next_pj':x('Próximos projetos','Next projects','Siguientes proyectos','Projets suivants','Progetti successivi','Nächste Projekte'),
})
# primeira pessoa (eu)
T.update({
'hero_p':x('Páginas sob medida, leves e prontas para vender, com suporte vitalício. Também crio artes para Instagram e eventos.','Custom, lightweight pages built to sell, with lifetime support. I also create Instagram and event graphics.','Páginas a medida, ligeras y listas para vender, con soporte de por vida. También creo diseños para Instagram y eventos.','Des pages sur mesure, légères et prêtes à vendre, avec support à vie. Je crée aussi des visuels pour Instagram et événements.','Pagine su misura, leggere e pronte a vendere, con assistenza a vita. Creo anche grafiche per Instagram ed eventi.','Maßgeschneiderte, schlanke Seiten, die verkaufen – mit lebenslangem Support. Außerdem gestalte ich Instagram- und Event-Grafiken.'),
'p_world':x('Atendo o mundo inteiro','I work with clients worldwide','Atiendo a todo el mundo','Je travaille avec le monde entier','Lavoro con tutto il mondo','Ich arbeite weltweit'),
'g1_p':x('Ou devolvo o seu dinheiro.*','Or I give your money back.*','O te devuelvo el dinero.*','Ou je vous rembourse.*','O ti restituisco i soldi.*','Oder ich erstatte Ihr Geld.*'),
'gar_feat':x('Sua landing page entregue em 24h ou devolvo o seu dinheiro.*','Your landing page delivered in 24h or I give your money back.*','Tu landing page entregada en 24h o te devuelvo el dinero.*','Votre landing page livrée en 24h ou je vous rembourse.*','La tua landing page consegnata in 24h o ti restituisco i soldi.*','Ihre Landingpage in 24h geliefert – oder ich erstatte Ihr Geld.*'),
'h1d':x('ou seu dinheiro de volta.','or your money back.','o te devuelvo el dinero.','ou remboursée.','o ti rimborso.','oder Geld zurück.'),
'g3_p':x('Madrugada, fim de semana ou feriado: eu respondo.',"Late night, weekend or holiday: I'll answer.",'Madrugada, fin de semana o feriado: yo respondo.','La nuit, le week-end ou un jour férié : je réponds.','Notte, weekend o festivi: rispondo io.','Nachts, am Wochenende oder an Feiertagen: Ich antworte.'),
'g2_p':x('Sua página nunca fica sozinha. Precisou, é só me chamar.','Your page is never on its own. Need anything? Just message me.','Tu página nunca queda sola. ¿Necesitas algo? Solo escríbeme.',"Votre page n'est jamais seule. Un besoin ? Écrivez-moi.",'La tua pagina non resta mai sola. Serve qualcosa? Scrivimi.','Ihre Seite ist nie allein. Brauchen Sie etwas? Schreiben Sie mir.'),
'gar_p':x('Você contrata comigo com segurança, do primeiro dia até muito depois da entrega.','Hire me with confidence, from day one to long after delivery.','Me contratas con seguridad, desde el primer día hasta mucho después de la entrega.',"Vous m'engagez en toute sérénité, du premier jour à bien après la livraison.",'Mi scegli in tutta sicurezza, dal primo giorno a molto dopo la consegna.','Beauftragen Sie mich sicher – vom ersten Tag bis lange nach der Übergabe.'),
'tr_nf_t':x('Emito nota fiscal','I issue official invoices','Emito factura oficial',"J'émets des factures",'Emetto fattura','Ich stelle offizielle Rechnungen aus'),
'tr_nf_p':x('Todo projeto sai com nota fiscal em nome da minha empresa.','Every project comes with an official invoice from my company.','Cada proyecto incluye factura oficial de mi empresa.','Chaque projet est facturé officiellement par mon entreprise.','Ogni progetto è accompagnato da fattura ufficiale della mia azienda.','Jedes Projekt wird offiziell von meinem Unternehmen abgerechnet.'),
's1_p':x('Você me conta o que precisa pelo WhatsApp, no seu idioma.','You tell me what you need on WhatsApp, in your language.','Me cuentas lo que necesitas por WhatsApp, en tu idioma.',"Vous me dites ce dont vous avez besoin sur WhatsApp, dans votre langue.",'Mi racconti cosa ti serve su WhatsApp, nella tua lingua.','Sie schildern mir Ihr Anliegen per WhatsApp, in Ihrer Sprache.'),
's3_p':x('Você acompanha o projeto e aprova cada etapa antes de eu seguir.','You follow the project and approve each step before I move on.','Sigues el proyecto y apruebas cada etapa antes de que yo avance.','Vous suivez le projet et validez chaque étape avant que je continue.','Segui il progetto e approvi ogni fase prima che io prosegua.','Sie verfolgen das Projekt und geben jeden Schritt frei, bevor ich weitermache.'),
's4_p':x('Cuido do domínio e da hospedagem e entrego tutoriais para você usar tudo com autonomia.','I handle the domain and hosting and give you tutorials to use everything on your own.','Me encargo del dominio y el hosting y te entrego tutoriales para usarlo todo con autonomía.',"Je gère le domaine et l'hébergement et vous fournis des tutoriels pour être autonome.",'Mi occupo di dominio e hosting e ti do tutorial per usare tutto in autonomia.','Ich kümmere mich um Domain und Hosting und liefere Tutorials, damit Sie alles selbst nutzen können.'),
's5_p':x('Depois da entrega, sigo com você. Precisou, é só me chamar.','After delivery I stay by your side. Need anything? Just message me.','Después de la entrega sigo contigo. ¿Necesitas algo? Solo escríbeme.',"Après la livraison, je reste à vos côtés. Un besoin ? Écrivez-moi.",'Dopo la consegna resto al tuo fianco. Serve qualcosa? Scrivimi.','Nach der Übergabe bleibe ich an Ihrer Seite. Schreiben Sie mir einfach.'),
'clients_eb':x('Clientes que já trabalharam comigo','Clients who have worked with me','Clientes que ya trabajaron conmigo',"Ils ont travaillé avec moi",'Clienti che hanno lavorato con me','Kunden, die mit mir gearbeitet haben'),
'fb_h':x('Quem já trabalhou comigo,','Clients who worked with me','Quien ya trabajó conmigo','Ceux qui ont travaillé avec moi','Chi ha già lavorato con me','Wer mit mir gearbeitet hat,'),
'fb_i':x('recomenda.','recommend me.','me recomienda.','me recommandent.','mi consiglia.','empfiehlt mich.'),
'fb_p':x('Prints e áudios reais de quem já trabalhou comigo.','Real screenshots and voice notes from people who have worked with me.','Capturas y audios reales de quienes ya trabajaron conmigo.','De vraies captures et notes vocales de ceux qui ont travaillé avec moi.','Screenshot e audio reali di chi ha già lavorato con me.','Echte Screenshots und Sprachnachrichten von Kunden, die mit mir gearbeitet haben.'),
'tr_ct_p':x('Contrato de prestação de serviço assinado: garantia e segurança para você e para mim.','A signed service contract: guarantees and security for both of us.','Contrato de servicio firmado: garantía y seguridad para ti y para mí.','Un contrat de prestation signé : garanties et sécurité pour vous comme pour moi.','Contratto di servizio firmato: garanzia e sicurezza per te e per me.','Unterschriebener Dienstleistungsvertrag: Garantie und Sicherheit für Sie und mich.'),
'ct_p':x('Me conta rapidinho o que você precisa. A conversa continua no WhatsApp.',"Tell me briefly what you need. We'll continue on WhatsApp.",'Cuéntame rápido lo que necesitas. Seguimos por WhatsApp.',"Dites-moi en deux mots ce dont vous avez besoin. On continue sur WhatsApp.",'Raccontami in breve cosa ti serve. Continuiamo su WhatsApp.','Sagen Sie mir kurz, was Sie brauchen. Wir sprechen auf WhatsApp weiter.'),
'reg_2p':x('O pagamento é de 100% antes do início do serviço.','Payment is 100% upfront, before I start.','El pago es del 100% antes de que empiece el servicio.','Le paiement est de 100 % avant que je commence.','Il pagamento è del 100% prima che io inizi.','Die Zahlung erfolgt zu 100 % vor Arbeitsbeginn.'),
})
T.update({
'see_pj':x('Ver projeto','View project','Ver proyecto','Voir le projet','Vedi progetto','Projekt ansehen'),
'show_more':x('Ver mais projetos','Show more projects','Ver más proyectos','Voir plus de projets','Vedi altri progetti','Weitere Projekte'),
'show_less':x('Mostrar menos','Show less','Mostrar menos','Voir moins','Mostra meno','Weniger anzeigen'),
'tab_d':x('Computador','Desktop','Escritorio','Ordinateur','Desktop','Desktop'),
'tab_m':x('Celular','Mobile','Móvil','Mobile','Mobile','Mobil'),
'tab_p':x('Painel de vendas','Sales dashboard','Panel de ventas','Tableau de ventes','Pannello vendite','Vertriebs-Dashboard'),
'hover_hint':x('Passe o mouse para ver a página rolando.','Hover to watch the page scroll.','Pasa el mouse para ver la página.','Survolez pour voir la page défiler.','Passa il mouse per vedere la pagina scorrere.','Darüberfahren, um die Seite scrollen zu sehen.'),
})

T.update({
'reg_1p':x('As 24h começam a contar quando o pagamento estiver confirmado e o briefing enviado. Se só um dos dois foi feito, o prazo ainda não corre.',
 'The 24h start once payment is confirmed and the briefing is sent. If only one of the two is done, the clock has not started yet.',
 'Las 24h empiezan a contar cuando el pago está confirmado y el briefing enviado. Si solo uno de los dos está hecho, el plazo aún no corre.',
 "Les 24h commencent quand le paiement est confirmé et le brief envoyé. Si un seul des deux est fait, le délai ne court pas encore.",
 "Le 24h iniziano quando il pagamento è confermato e il briefing inviato. Se manca uno dei due, il termine non decorre ancora.",
 'Die 24h beginnen, sobald die Zahlung bestätigt und das Briefing gesendet ist. Fehlt eines davon, läuft die Frist noch nicht.'),
'reg_4t':x('O que chega em 24h','What arrives in 24h','Qué llega en 24h','Ce qui arrive en 24h','Cosa arriva in 24h','Was in 24h kommt'),
'reg_4p':x('Em 24h você recebe a primeira versão do site, completa e funcionando. A partir dela fazemos juntos os ajustes até a versão final.',
 'In 24h you get the first version of the site, complete and working. From there we make the adjustments together until the final version.',
 'En 24h recibes la primera versión del sitio, completa y funcionando. A partir de ella hacemos juntos los ajustes hasta la versión final.',
 "En 24h, vous recevez la première version du site, complète et fonctionnelle. Ensuite, nous faisons ensemble les ajustements jusqu'à la version finale.",
 'In 24h ricevi la prima versione del sito, completa e funzionante. Da lì facciamo insieme le modifiche fino alla versione finale.',
 'In 24h erhalten Sie die erste Version der Seite, vollständig und funktionsfähig. Danach nehmen wir gemeinsam die Anpassungen bis zur finalen Version vor.'),
})

T.update({'lp_cta':x('Quero criar minha landing page','I want my own landing page','Quiero crear mi landing page','Je veux créer ma landing page','Voglio creare la mia landing page','Ich will meine Landingpage')})

T.update({'hero_p':x('Páginas sob medida, leves e prontas para vender, com suporte vitalício.','Custom, lightweight pages built to sell, with lifetime support.','Páginas a medida, ligeras y listas para vender, con soporte de por vida.','Des pages sur mesure, légères et prêtes à vendre, avec support à vie.','Pagine su misura, leggere e pronte a vendere, con assistenza a vita.','Maßgeschneiderte, schlanke Seiten, die verkaufen – mit lebenslangem Support.')})

T.update({
'nav_lp':x('Páginas','Pages','Páginas','Pages','Pagine','Seiten'),
'h1a':x('Sua página','Your page','Tu página','Votre page','La tua pagina','Ihre Seite'),
'cta_lp':x('Quero uma página','I want a page','Quiero una página','Je veux une page','Voglio una pagina','Ich möchte eine Seite'),
'gni_desc':x('Página de intercâmbio para brasileiros que querem morar na Irlanda, com painel de vendas para a equipe.','Study-abroad page for Brazilians moving to Ireland, with a sales dashboard for the team.','Página de intercambio para brasileños que quieren vivir en Irlanda, con panel de ventas para el equipo.',"Page d'études à l'étranger pour Brésiliens partant vivre en Irlande, avec tableau de ventes pour l'équipe.","Pagina di studio all'estero per brasiliani che vogliono vivere in Irlanda, con pannello vendite per il team.",'Seite für Auslandsaufenthalte von Brasilianern in Irland – mit Vertriebs-Dashboard fürs Team.'),
'invest_sub':x('Página + painel de vendas','Page + sales dashboard','Página + panel de ventas','Page + tableau de ventes','Pagina + pannello vendite','Seite + Vertriebs-Dashboard'),
'gar_eb':x('Garantias de toda página','Guarantees on every page','Garantías en cada página','Garanties sur chaque page','Garanzie su ogni pagina','Garantien für jede Seite'),
'svc_lp':x('Página','Page','Página','Page','Pagina','Seite'),
'f_msg_ph':x('Ex.: página para captar clientes da minha clínica','E.g. a page to get clients for my clinic','Ej.: página para captar clientes de mi clínica','Ex. : une page pour attirer des clients à mon cabinet','Es.: pagina per trovare clienti per la mia clinica','Z. B. Seite für neue Kunden meiner Praxis'),
'wa_lp':x('Olá, Pedro! Tenho interesse em uma página.',"Hi Pedro! I'm interested in a page.",'¡Hola, Pedro! Me interesa una página.','Bonjour Pedro ! Je suis intéressé(e) par une page.','Ciao Pedro! Mi interessa una pagina.','Hallo Pedro! Ich interessiere mich für eine Seite.'),
'gar_feat':x('Sua página entregue em 24h ou devolvo o seu dinheiro.*','Your page delivered in 24h or I give your money back.*','Tu página entregada en 24h o te devuelvo el dinero.*','Votre page livrée en 24h ou je vous rembourse.*','La tua pagina consegnata in 24h o ti restituisco i soldi.*','Ihre Seite in 24h geliefert – oder ich erstatte Ihr Geld.*'),
'next_p':x('Página sob medida, entregue em 24h*.','Custom page, delivered in 24h*.','Página a medida, entregada en 24h*.','Page sur mesure, livrée en 24h*.','Pagina su misura, consegnata in 24h*.','Maßgeschneiderte Seite, in 24h* geliefert.'),
'st_t':x('Página entregue em 24h*','Page delivered in 24h*','Página entregada en 24h*','Page livrée en 24h*','Pagina consegnata in 24h*','Seite in 24h* geliefert'),
'lp_cta':x('Quero criar minha página','I want my own page','Quiero crear mi página','Je veux créer ma page','Voglio creare la mia pagina','Ich will meine eigene Seite'),
})

T.update({'reg_4p':x('Em 24h você recebe a primeira versão da página, completa e funcionando. A partir dela fazemos juntos os ajustes até a versão final.',
 'In 24h you get the first version of your page, complete and working. From there we make the adjustments together until the final version.',
 'En 24h recibes la primera versión de la página, completa y funcionando. A partir de ella hacemos juntos los ajustes hasta la versión final.',
 "En 24h, vous recevez la première version de la page, complète et fonctionnelle. Ensuite, nous faisons ensemble les ajustements jusqu'à la version finale.",
 'In 24h ricevi la prima versione della pagina, completa e funzionante. Da lì facciamo insieme le modifiche fino alla versione finale.',
 'In 24h erhalten Sie die erste Version der Seite, vollständig und funktionsfähig. Danach nehmen wir gemeinsam die Anpassungen bis zur finalen Version vor.')})

T.update({
'tag_inst':x('Site institucional','Business website','Sitio institucional','Site vitrine','Sito aziendale','Unternehmensseite'),
'tag_map':x('Mapa de unidades','Locations map','Mapa de unidades','Carte des points de vente','Mappa delle sedi','Standortkarte'),
'pd_desc':x('Página de oficina especializada em ARLA 32 e injeção diesel, com serviços, unidades e orçamento direto no WhatsApp.','Page for a workshop specialized in AdBlue (ARLA 32) and diesel injection, with services, locations and quotes straight on WhatsApp.','Página de taller especializado en ARLA 32 e inyección diésel, con servicios, unidades y presupuesto directo por WhatsApp.',"Page d'un garage spécialisé en AdBlue (ARLA 32) et injection diesel, avec services, adresses et devis direct sur WhatsApp.",'Pagina di officina specializzata in AdBlue (ARLA 32) e iniezione diesel, con servizi, sedi e preventivo diretto su WhatsApp.','Seite für eine Werkstatt für AdBlue (ARLA 32) und Dieseleinspritzung – mit Leistungen, Standorten und Angebot direkt per WhatsApp.'),
'cap_desc':x('Mapa interativo da rede de franquias, com busca por cidade, unidades por estado e ficha completa de cada loja.','Interactive map of the franchise network, with city search, locations by state and a full profile for each store.','Mapa interactivo de la red de franquicias, con búsqueda por ciudad, unidades por estado y ficha completa de cada tienda.','Carte interactive du réseau de franchises, avec recherche par ville, points de vente par État et fiche complète de chaque magasin.','Mappa interattiva della rete in franchising, con ricerca per città, sedi per stato e scheda completa di ogni negozio.','Interaktive Karte des Franchise-Netzes mit Stadtsuche, Standorten nach Bundesstaat und vollständigem Profil jeder Filiale.'),
})

T.update({
'pd_f1':x('Orçamento direto no WhatsApp','Quotes straight on WhatsApp','Presupuesto directo por WhatsApp','Devis direct sur WhatsApp','Preventivo diretto su WhatsApp','Angebot direkt per WhatsApp'),
'pd_f2':x('Serviços com contato em cada card','Services with a contact button on each card','Servicios con contacto en cada tarjeta','Services avec contact sur chaque carte','Servizi con contatto in ogni scheda','Leistungen mit Kontakt auf jeder Karte'),
'pd_f3':x('Unidades com rota pelo mapa','Locations with map directions','Unidades con ruta en el mapa','Adresses avec itinéraire sur la carte','Sedi con indicazioni sulla mappa','Standorte mit Routenplanung'),
'pd_sub':x('Página institucional completa','Full business website','Página institucional completa','Site vitrine complet','Sito aziendale completo','Komplette Unternehmensseite'),
'cap_f1':x('Mapa do Brasil interativo','Interactive map of Brazil','Mapa interactivo de Brasil','Carte interactive du Brésil','Mappa interattiva del Brasile','Interaktive Brasilienkarte'),
'cap_f2':x('Busca por cidade ou estado','Search by city or state','Búsqueda por ciudad o estado','Recherche par ville ou État','Ricerca per città o stato','Suche nach Stadt oder Bundesstaat'),
'cap_f3':x('Ficha completa de cada loja','Full profile for every store','Ficha completa de cada tienda','Fiche complète de chaque magasin','Scheda completa di ogni negozio','Vollständiges Profil jeder Filiale'),
'cap_sub':x('Mapa interativo da rede','Interactive network map','Mapa interactivo de la red','Carte interactive du réseau','Mappa interattiva della rete','Interaktive Netzwerkkarte'),
})

T.update({
'tag_order':x('Pedidos online','Online orders','Pedidos online','Commandes en ligne','Ordini online','Online-Bestellungen'),
'tag_catalog':x('Catálogo online','Online catalog','Catálogo online','Catalogue en ligne','Catalogo online','Online-Katalog'),
'tag_agency':x('Site de agência','Agency website','Sitio de agencia',"Site d'agence",'Sito di agenzia','Agentur-Website'),
'vc_desc':x('Página de confeitaria com montador de bolo, valor calculado na hora e pedido pronto no WhatsApp.','Bakery page with a cake builder, instant pricing and the order ready to send on WhatsApp.','Página de repostería con armador de pasteles, precio al instante y pedido listo en WhatsApp.','Page de pâtisserie avec configurateur de gâteau, prix instantané et commande prête sur WhatsApp.','Pagina di pasticceria con configuratore di torte, prezzo immediato e ordine pronto su WhatsApp.','Konditorei-Seite mit Tortenkonfigurator, Sofortpreis und fertiger Bestellung per WhatsApp.'),
'vc_f1':x('Monte seu bolo com valor na hora','Build your cake with instant pricing','Arma tu pastel con precio al instante','Composez votre gâteau avec prix immédiat','Componi la tua torta con prezzo immediato','Torte zusammenstellen mit Sofortpreis'),
'vc_f2':x('Pedido pronto no WhatsApp','Order ready on WhatsApp','Pedido listo en WhatsApp','Commande prête sur WhatsApp','Ordine pronto su WhatsApp','Bestellung fertig per WhatsApp'),
'vc_f3':x('Galeria de bolos e avaliações','Cake gallery and reviews','Galería de pasteles y reseñas','Galerie de gâteaux et avis','Galleria di torte e recensioni','Tortengalerie und Bewertungen'),
'vc_sub':x('Página com montador de pedidos','Page with an order builder','Página con armador de pedidos','Page avec configurateur de commande','Pagina con configuratore di ordini','Seite mit Bestellkonfigurator'),
'yad_desc':x('Catálogo de perfumaria com busca por notas, vitrine completa e carrinho que envia o pedido pelo WhatsApp.','Perfume store catalog with search by notes, a full showcase and a cart that sends the order via WhatsApp.','Catálogo de perfumería con búsqueda por notas, vitrina completa y carrito que envía el pedido por WhatsApp.','Catalogue de parfumerie avec recherche par notes, vitrine complète et panier qui envoie la commande sur WhatsApp.','Catalogo di profumeria con ricerca per note, vetrina completa e carrello che invia l\'ordine su WhatsApp.','Parfümerie-Katalog mit Suche nach Duftnoten, kompletter Auslage und Warenkorb, der die Bestellung per WhatsApp sendet.'),
'yad_f1':x('Busca de perfumes por nota','Search perfumes by note','Búsqueda de perfumes por nota','Recherche de parfums par note','Ricerca dei profumi per nota','Parfümsuche nach Duftnote'),
'yad_f2':x('Vitrine completa com filtros','Full showcase with filters','Vitrina completa con filtros','Vitrine complète avec filtres','Vetrina completa con filtri','Komplette Auslage mit Filtern'),
'yad_f3':x('Carrinho com pedido no WhatsApp','Cart with order on WhatsApp','Carrito con pedido por WhatsApp','Panier avec commande sur WhatsApp','Carrello con ordine su WhatsApp','Warenkorb mit Bestellung per WhatsApp'),
'yad_sub':x('Catálogo com carrinho','Catalog with cart','Catálogo con carrito','Catalogue avec panier','Catalogo con carrello','Katalog mit Warenkorb'),
'lf_desc':x('Site de agência de tráfego e automação, com cases, resultados em números e método em etapas.','Website for a traffic and automation agency, with case studies, results in numbers and a step-by-step method.','Sitio de agencia de tráfico y automatización, con casos, resultados en números y método por etapas.',"Site d'agence d'acquisition et d'automatisation, avec études de cas, résultats chiffrés et méthode en étapes.",'Sito di agenzia di traffico e automazione, con casi studio, risultati in numeri e metodo a fasi.','Website einer Agentur für Traffic und Automatisierung – mit Cases, Ergebnissen in Zahlen und Methode in Schritten.'),
'lf_f1':x('Cases com resultados reais','Case studies with real results','Casos con resultados reales','Études de cas aux résultats réels','Casi studio con risultati reali','Cases mit echten Ergebnissen'),
'lf_f2':x('Números em destaque','Key numbers up front','Números destacados','Chiffres mis en avant','Numeri in evidenza','Zahlen im Fokus'),
'lf_f3':x('Contato direto no WhatsApp','Direct contact on WhatsApp','Contacto directo por WhatsApp','Contact direct sur WhatsApp','Contatto diretto su WhatsApp','Direkter Kontakt per WhatsApp'),
'lf_sub':x('Site institucional','Business website','Sitio institucional','Site vitrine','Sito aziendale','Unternehmensseite'),
})

_vo={'pt':('Eventos esportivos','Artes das provas de corrida: lançamentos, premiação, inscrições e calendário de eventos.'),
'en':('Sports events','Graphics for running races: launches, awards, registrations and the event calendar.'),
'es':('Eventos deportivos','Piezas para carreras: lanzamientos, premiación, inscripciones y calendario de eventos.'),
'fr':('Événements sportifs',"Visuels pour des courses : lancements, remise des prix, inscriptions et calendrier des épreuves."),
'it':('Eventi sportivi','Grafiche per gare di corsa: lanci, premiazioni, iscrizioni e calendario degli eventi.'),
'de':('Sportevents','Grafiken für Laufevents: Ankündigungen, Siegerehrung, Anmeldungen und Eventkalender.')}
for _l in _vo:
    A['clients'][_l]=list(A['clients'][_l]); A['clients'][_l].insert(1,_vo[_l])

T.update({
'pm_with':x('Projeto feito em conjunto com','Project made together with','Proyecto hecho en conjunto con','Projet réalisé en collaboration avec','Progetto realizzato insieme a','Projekt in Zusammenarbeit mit'),
})

T.update({
'nav_gar':x('Garantias','Guarantees','Garantías','Garanties','Garanzie','Garantien'),
'cta_work':x('Ver projetos','See projects','Ver proyectos','Voir les projets','Vedi i progetti','Projekte ansehen'),
'ch1_eb':x('Páginas sob medida','Custom-built pages','Páginas a medida','Pages sur mesure','Pagine su misura','Maßgeschneiderte Seiten'),
})

T.update({
'nav_about':x('Sobre','About','Sobre mí','À propos','Chi sono','Über mich'),
'nav_links':x('Links','Links','Enlaces','Liens','Link','Links'),
'ab_eb':x('Sobre mim','About me','Sobre mí','À propos','Chi sono','Über mich'),
'ab_h':x('Prazer, eu sou o','Nice to meet you, I’m','Encantado, soy','Enchanté, je suis','Piacere, sono','Freut mich, ich bin'),
'ab_i':x('Pedro.','Pedro.','Pedro.','Pedro.','Pedro.','Pedro.'),
'ab_role':x('Designer · fundador da Pedro Design','Designer · founder of Pedro Design','Diseñador · fundador de Pedro Design','Designer · fondateur de Pedro Design','Designer · fondatore di Pedro Design','Designer · Gründer von Pedro Design'),
'ab_p1':x('Sou designer e crio páginas sob medida para marcas que querem ser levadas a sério na internet. Cada projeto passa pelas minhas mãos, do primeiro oi até muito depois do ar.',
 'I’m a designer and I build custom pages for brands that want to be taken seriously online. Every project goes through my hands, from the first hello to long after launch.',
 'Soy diseñador y creo páginas a medida para marcas que quieren ser tomadas en serio en internet. Cada proyecto pasa por mis manos, desde el primer hola hasta mucho después del lanzamiento.',
 'Je suis designer et je crée des pages sur mesure pour les marques qui veulent être prises au sérieux en ligne. Chaque projet passe entre mes mains, du premier bonjour à bien après la mise en ligne.',
 'Sono un designer e creo pagine su misura per i brand che vogliono essere presi sul serio online. Ogni progetto passa dalle mie mani, dal primo saluto a molto dopo la pubblicazione.',
 'Ich bin Designer und gestalte maßgeschneiderte Seiten für Marken, die online ernst genommen werden wollen. Jedes Projekt geht durch meine Hände, vom ersten Hallo bis lange nach dem Launch.'),
'ab_p2':x('Sem intermediários e sem modelos prontos: você fala direto comigo, e eu cuido de cada detalhe para a sua página transmitir confiança e transformar visita em cliente.',
 'No middlemen and no ready-made templates: you talk directly to me, and I take care of every detail so your page builds trust and turns visitors into clients.',
 'Sin intermediarios ni plantillas: hablas directamente conmigo y yo cuido cada detalle para que tu página transmita confianza y convierta visitas en clientes.',
 'Sans intermédiaire ni modèle tout fait : vous parlez directement avec moi, et je soigne chaque détail pour que votre page inspire confiance et transforme les visites en clients.',
 'Niente intermediari né modelli pronti: parli direttamente con me e curo ogni dettaglio perché la tua pagina trasmetta fiducia e trasformi le visite in clienti.',
 'Keine Vermittler und keine fertigen Vorlagen: Sie sprechen direkt mit mir, und ich kümmere mich um jedes Detail, damit Ihre Seite Vertrauen schafft und Besucher zu Kunden macht.'),
'ab_f1':x('Atendimento direto comigo, do início ao fim','You work directly with me, start to finish','Atención directa conmigo, de principio a fin','Vous échangez directement avec moi, du début à la fin','Lavori direttamente con me, dall’inizio alla fine','Sie arbeiten direkt mit mir, von Anfang bis Ende'),
'ab_f2':x('Empresa com CNPJ e nota fiscal','Registered company with official invoices','Empresa registrada con factura oficial','Entreprise enregistrée, avec facture officielle','Azienda registrata, con fattura','Eingetragenes Unternehmen mit offizieller Rechnung'),
'ab_f3':x('Clientes no Brasil e no exterior','Clients in Brazil and abroad','Clientes en Brasil y en el exterior','Des clients au Brésil et à l’étranger','Clienti in Brasile e all’estero','Kunden in Brasilien und im Ausland'),
'ab_cta':x('Falar comigo','Talk to me','Hablar conmigo','Me contacter','Parla con me','Mit mir sprechen'),
'stats_lbl':x('Números','Numbers','Números','Chiffres','Numeri','Zahlen'),
'stat_24':x('para a primeira versão da sua página*','for the first version of your page*','para la primera versión de tu página*','pour la première version de votre page*','per la prima versione della tua pagina*','für die erste Version Ihrer Seite*'),
'stat_100':x('sob medida, sem modelos prontos','custom-built, no ready-made templates','a medida, sin plantillas','sur mesure, sans modèle tout fait','su misura, senza modelli pronti','maßgeschneidert, ohne fertige Vorlagen'),
'stat_15':x('marcas que já confiaram no meu trabalho','brands that have trusted my work','marcas que ya confiaron en mi trabajo','marques qui m’ont déjà fait confiance','brand che si sono già affidati a me','Marken, die mir bereits vertraut haben'),
'stat_247':x('de suporte, inclusive fim de semana','support, weekends included','de soporte, incluso fines de semana','de support, week-end compris','di supporto, anche nel weekend','Support, auch am Wochenende'),
})

T.update({
'ab_f2':x('Cada detalhe revisado antes de ir ao ar','Every detail reviewed before going live','Cada detalle revisado antes de publicar','Chaque détail vérifié avant la mise en ligne','Ogni dettaglio rivisto prima della pubblicazione','Jedes Detail geprüft, bevor es live geht'),
})

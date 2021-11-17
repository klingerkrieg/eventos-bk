<?php

class Database_model extends CI_Model {

    function creat_all_if_not_exists(){
        $this->create_tables_if_not_exists();
        $this->insert_data_if_empty();
    }

    function create_tables_if_not_exists(){
        
        $sql = "SELECT count(*) AS qtd FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = '{$this->db->database}' ";
        
        $query = $this->db->query($sql);
        $row = $query->row_array();


        if ($row["qtd"] == 0){

            $sql = "CREATE TABLE IF NOT EXISTS cursos (
                id int(11) NOT NULL AUTO_INCREMENT,
                curso varchar(250) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS instituicoes (
                id int(11) NOT NULL AUTO_INCREMENT,
                instituicao varchar(250) NOT NULL,
                sigla varchar(10) NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);


            $sql ="CREATE TABLE IF NOT EXISTS paises (
                id int(11) NOT NULL AUTO_INCREMENT,
                pais varchar(250) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);


            $sql = "CREATE TABLE IF NOT EXISTS ufs (
                id int(11) NOT NULL AUTO_INCREMENT,
                uf varchar(2) NOT NULL,
                idpais int(11) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY paises_idx (idpais)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE ufs
                    ADD CONSTRAINT paises FOREIGN KEY (idpais) REFERENCES paises (id);";
            $this->db->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS cidades (
                id int(11) NOT NULL AUTO_INCREMENT,
                cidade varchar(250) NOT NULL,
                iduf int(11) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY ufs_idx (iduf)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE cidades
                    ADD CONSTRAINT ufs FOREIGN KEY (iduf) REFERENCES ufs (id);";
            $this->db->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS bairros (
                id int(11) NOT NULL AUTO_INCREMENT,
                bairro varchar(250) NOT NULL,
                idcidade int(11) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY cidades_idx (idcidade)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE bairros
                    ADD CONSTRAINT cidades FOREIGN KEY (idcidade) REFERENCES cidades (id);";
            $this->db->query($sql);


            

            $nivel = NIVEL_PARTICIPANTE;
            $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id int(11) NOT NULL AUTO_INCREMENT,
                nome_completo varchar(250) NOT NULL,
                nome_social varchar(200) NULL,
                email varchar(250) NOT NULL,
                password varchar(40) NULL,
                cpf varchar(14) NULL,
                matricula varchar(50) NULL,
                foto varchar(100) NULL,
                pago tinyint(1) NOT NULL DEFAULT '0',
                tipoInscricao int(11) NULL,
                aprovado_certificado_participante tinyint(1) NOT NULL DEFAULT '0',
                aprovado_certificado_avaliador tinyint(1) NOT NULL DEFAULT '0',
                aprovado_certificado_palestrante tinyint(1) NOT NULL DEFAULT '0',
                aprovado_certificado_mesa_redonda tinyint(1) NOT NULL DEFAULT '0',
                certificado_participante varchar(250) NULL,
                certificado_participante_data timestamp NULL DEFAULT NULL,
                certificado_avaliador varchar(250) NULL,
                certificado_avaliador_data timestamp NULL DEFAULT NULL,
                titulo_palestra varchar(250) NULL,
                certificado_palestrante varchar(250) NULL,
                certificado_palestrante_data timestamp NULL DEFAULT NULL,
                titulo_mesa_redonda varchar(250) NULL,
                certificado_mesa_redonda varchar(250) NULL,
                certificado_mesa_redonda_data timestamp NULL DEFAULT NULL,
                logradouro varchar(250) NULL,
                lattes varchar(250) NULL,
                curriculo varchar(1000) NULL,
                telefone varchar(15) NULL, 
                numero varchar(10) NULL,
                cep varchar(9) NULL,
                idbairro int(11) DEFAULT NULL,
                idcurso int(11) DEFAULT NULL,
                idnivelcurso int(11) NULL,
                idinstituicao int(11) DEFAULT NULL,
                outra_instituicao  varchar(250) NULL,
                nivel int(11) DEFAULT '$nivel',
                email_confirmado tinyint(4) NOT NULL DEFAULT '0',
                deleted tinyint(4) NOT NULL DEFAULT '0',
                hash varchar(40) DEFAULT NULL,
                criacao timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY email_UNIQUE (email),
                KEY bairros_idx (idbairro),
                KEY cursos_idx (idcurso),
                KEY instituicoes_idx (idinstituicao)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE usuarios
                    ADD CONSTRAINT bairros FOREIGN KEY (idbairro) REFERENCES bairros (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
                    ADD CONSTRAINT cursos FOREIGN KEY (idcurso) REFERENCES cursos (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
                    ADD CONSTRAINT instituicoes FOREIGN KEY (idinstituicao) REFERENCES instituicoes (id) ON DELETE NO ACTION ON UPDATE NO ACTION;";
            $this->db->query($sql);


            

            


            $sql = "CREATE TABLE IF NOT EXISTS log (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    descricao varchar(200) NOT NULL,
                    sql_code varchar(500) NOT NULL,
                    iduser int(11) NULL,
                    data_hora timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    tabela varchar(45) NOT NULL,
                    PRIMARY KEY (id),
                    KEY log_use_idx (iduser)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);
        

            $sql = "ALTER TABLE log
                    ADD CONSTRAINT log_use FOREIGN KEY (iduser) REFERENCES usuarios (id);";
            $this->db->query($sql);


            $sql = "CREATE TABLE IF NOT EXISTS eventos (
                id int(11) NOT NULL AUTO_INCREMENT,
                evento varchar(250) NOT NULL,
                email varchar(250) NOT NULL,
                data_inicio timestamp not null default '2021-04-13',
                data_fim timestamp not null default '2021-04-15',
                data_certificado varchar(250) NOT NULL DEFAULT '01 de Janeiro de 2021',
                aceitando_submissoes tinyint(4) NOT NULL DEFAULT '1',
                submissoes_ate timestamp not null default '2021-03-31',
                aceitando_correcoes tinyint(4) NOT NULL DEFAULT '0',
                correcoes_ate timestamp not null default '2021-04-09',
                aceitando_submissoes_minicursos tinyint(4) NOT NULL DEFAULT '1',
                aceitando_matriculas_minicursos tinyint(4) NOT NULL DEFAULT '1',
                minicursos_ate timestamp not null default '2021-03-19',
                matriculas_ate timestamp not null default '2021-03-19',
                evento_encerrado tinyint(4) NOT NULL DEFAULT '0',
                aberto_ate timestamp not null default '2021-04-15',
            #    aceitando_correcoes_minicursos tinyint(4) NOT NULL DEFAULT '0',
                deleted tinyint(4) NOT NULL DEFAULT '0',
                limite_submissoes int(11) NOT NULL DEFAULT '2',
                limite_coautores int(11) NOT NULL DEFAULT '2',
                limite_orientadores int(11) NOT NULL DEFAULT '2',
                limite_avaliadores_trabalhos int(11) NOT NULL DEFAULT '2',
                limite_submissoes_minicursos int(11) NOT NULL DEFAULT '2',
                limite_coautores_minicursos int(11) NOT NULL DEFAULT '2',
                PRIMARY KEY (id)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);
    


            $sql = "CREATE TABLE IF NOT EXISTS gts (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    gt varchar(250) NOT NULL,
                    deleted tinyint(4) NOT NULL DEFAULT '0',
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
              $this->db->query($sql);


            $sql = "CREATE TABLE IF NOT EXISTS grandes_areas (
                id int(11) NOT NULL AUTO_INCREMENT,
                grande_area varchar(250) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                alteracao timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
              $this->db->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS areas (
                id int(11) NOT NULL AUTO_INCREMENT,
                area varchar(250) NOT NULL,
                idgrandearea int(11) NOT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                alteracao timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
              $this->db->query($sql);

            $sql = "ALTER TABLE areas
                    ADD CONSTRAINT gareas FOREIGN KEY (idgrandearea) REFERENCES grandes_areas (id)";
            $this->db->query($sql);
      

            /**
             * Trabalhos
             */
  
            

            $sql = "CREATE TABLE IF NOT EXISTS trabalhos (
                id int(11) NOT NULL AUTO_INCREMENT,
                titulo varchar(250) NOT NULL,
                arquivo varchar(250) NOT NULL,
                arquivoCorrigido varchar(250) DEFAULT NULL,
                url varchar(2000) DEFAULT NULL,
                correcao timestamp NULL,
                observacao varchar(8000) DEFAULT NULL,
                submissao timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                premiado tinyint(4) NOT NULL DEFAULT '0',
                apresentado tinyint(4) NOT NULL DEFAULT '0',
                idarea int(11) NULL,
                idtrilha int(11) NULL,
                idtipo_trabalho int(11) NULL,
                idgt int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                certificado varchar(250) NULL,
                certificado_data timestamp NULL DEFAULT NULL,
                status int(11) NOT NULL DEFAULT '0',
                deleted tinyint(4) NOT NULL DEFAULT '0',
                alteracao timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY usuarios_t_idx (idusuario),
                KEY gts_t_idx (idgt)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
              $this->db->query($sql);

              $sql = "ALTER TABLE trabalhos
                ADD CONSTRAINT gts_t FOREIGN KEY (idgt) REFERENCES gts (id),
                ADD CONSTRAINT areas_t FOREIGN KEY (idarea) REFERENCES areas (id),
                ADD CONSTRAINT usuarios_t FOREIGN KEY (idusuario) REFERENCES usuarios (id);";
              $this->db->query($sql);


              #orientador coautor
            $sql = "CREATE TABLE IF NOT EXISTS coautores (
                id int(11) NOT NULL AUTO_INCREMENT,
                idtrabalho int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                certificado varchar(250) NULL,
                certificado_data timestamp NULL DEFAULT NULL,
                tipo tinyint(4) NOT NULL, #0 coautor - 1 orientador
                ciente tinyint(4) NULL,
                ciente_hash varchar(30) NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY usuarios_idx (idusuario),
                KEY trabalhos_idx (idtrabalho)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE coautores
                    ADD CONSTRAINT usuarios_c FOREIGN KEY (idusuario) REFERENCES usuarios (id),
                    ADD CONSTRAINT trabalhos_c FOREIGN KEY (idtrabalho) REFERENCES trabalhos (id);";
            $this->db->query($sql);


            $pendente = PENDENTE;//5
            $sql = "CREATE TABLE IF NOT EXISTS avaliadores (
                id int(11) NOT NULL AUTO_INCREMENT,
                idtrabalho int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                observacao varchar(8000) NULL,
                status int(11) NOT NULL DEFAULT '$pendente',
                nota int(11) NOT NULL,
                deleted tinyint(4) NOT NULL,
                PRIMARY KEY (id),
                KEY usuarios_idx (idusuario),
                KEY trabalhos_idx (idtrabalho)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE avaliadores
                    ADD CONSTRAINT usuarios_av FOREIGN KEY (idusuario) REFERENCES usuarios (id),
                    ADD CONSTRAINT trabalhos_av FOREIGN KEY (idtrabalho) REFERENCES trabalhos (id);";
            $this->db->query($sql);


            $sql = "CREATE TABLE IF NOT EXISTS avaliadores_areas (
                id int(11) NOT NULL AUTO_INCREMENT,
                idarea int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                PRIMARY KEY (id),
                KEY usuarios_idx (idusuario),
                KEY areas_idx (idarea)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE avaliadores_areas
                    ADD CONSTRAINT usuarios_ar FOREIGN KEY (idusuario) REFERENCES usuarios (id),
                    ADD CONSTRAINT areas_ar FOREIGN KEY (idarea) REFERENCES areas (id);";
            $this->db->query($sql);


            /** Minicursos */

            $sql = "CREATE TABLE IF NOT EXISTS minicursos (
                id int(11) NOT NULL AUTO_INCREMENT,
                titulo varchar(250) NOT NULL,
                arquivo varchar(250) NOT NULL,
                resumo varchar(2000) DEFAULT NULL, 
                objetivo varchar(500) DEFAULT NULL, 
                descricao varchar(800) DEFAULT NULL, 
                informacoes_adicionais varchar(800) DEFAULT NULL, #qualquer observação que o inscrito queira fornecer
                observacao varchar(800) DEFAULT NULL,  #observações dos revisores
                submissao timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                idusuario int(11) NOT NULL,
                idarea int(11) NOT NULL,
                certificado varchar(250) NULL,
                certificado_data timestamp NULL DEFAULT NULL,
                status int(11) NOT NULL DEFAULT '0',
                vagas int(11) NULL,
                ch float(11) NULL,
                url varchar(2000) NULL,
                matricula_disponivel tinyint(4) NOT NULL DEFAULT '0',
                horarios_preferenciais varchar(250) NULL,
                horarios_escolhidos varchar(250) NULL,
                sala varchar(100) NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                alteracao timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY usuarios_m_idx (idusuario),
                KEY areas_m_idx (idarea)
              ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
              $this->db->query($sql);

            $sql = "ALTER TABLE minicursos
                ADD CONSTRAINT areas_m FOREIGN KEY (idarea) REFERENCES areas (id),
                ADD CONSTRAINT usuarios_m FOREIGN KEY (idusuario) REFERENCES usuarios (id);";
              $this->db->query($sql);

              $sql = "CREATE TABLE IF NOT EXISTS minicursos_coautores (
                id int(11) NOT NULL AUTO_INCREMENT,
                idminicurso int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                certificado varchar(250) NULL,
                certificado_data timestamp NULL DEFAULT NULL,
                ciente tinyint(4) NULL,
                ciente_hash varchar(30) NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY usuarios_mc_idx (idusuario),
                KEY minicursos_mc_idx (idminicurso)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE minicursos_coautores
                    ADD CONSTRAINT usuarios_mc FOREIGN KEY (idusuario) REFERENCES usuarios (id),
                    ADD CONSTRAINT minicursos_mc FOREIGN KEY (idminicurso) REFERENCES minicursos (id);";
            $this->db->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS matriculas (
                id int(11) NOT NULL AUTO_INCREMENT,
                idminicurso int(11) NOT NULL,
                idusuario int(11) NOT NULL,
                presenca tinyint(4) NOT NULL DEFAULT '0',
                aprovado tinyint(4) NOT NULL DEFAULT '0',
                certificado varchar(250) NULL,
                certificado_data timestamp NULL DEFAULT NULL,
                deleted tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                KEY usuarios_mc_idx (idusuario),
                KEY minicursos_mc_idx (idminicurso)
                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
            $this->db->query($sql);

            $sql = "ALTER TABLE matriculas
                    ADD CONSTRAINT usuarios_mat FOREIGN KEY (idusuario) REFERENCES usuarios (id),
                    ADD CONSTRAINT minicursos_mat FOREIGN KEY (idminicurso) REFERENCES minicursos (id);";
            $this->db->query($sql);

              


            
                

        }
        
    }


    function insert_data_if_empty(){
        
        $this->insert_evento();
        $this->insert_gts();
        $this->insert_instituicoes();
        $this->insert_cursos();
        $this->insert_paises();
        $this->insert_usuarios();
        $this->insert_test_usuarios();
        $this->insert_areas();

        #cidades e bairros são inseridos a medida que o sistema vai sendo utilizado
        #a partir do WS dos correios
    }

    function insert_evento(){
        $query = $this->db->query("select count(*) as qtd from eventos");
        $result = $query->row_array();

        if ($result["qtd"] == 0){
            $sql = "INSERT INTO eventos (id, evento, email) VALUES
                    (1, 'VII EXPOTEC', 'viiexpotec@gmail.com');";
            $this->db->query($sql);
        }
    }

    function insert_gts(){
        $query = $this->db->query("select count(*) as qtd from gts");
        $result = $query->row_array();

        if ($result["qtd"] == 0){
            $sql = "INSERT INTO gts (id, gt, deleted) VALUES
              (1, 'GT 01 – Currículo e formação de professores', 0),
              (2, 'GT 02 – Políticas Educacionais na formação docente', 0),
              (3, 'GT 03 – Práticas pedagógicas e formação docente', 0),
              (4, 'GT 04 – Tecnologias Digitais de Informação e Comunicação  na Educação', 0),
              (5, 'GT 05 – Diversidade, inclusão e direitos humanos na formação de professores', 0);";
              $this->db->query($sql);
        }
    }

    function insert_cursos(){

        $query = $this->db->query("select count(*) as qtd from cursos");
        $result = $query->row_array();

        if ($result["qtd"] != 0){
            return;
        }

        #$niveis = [1=>"Técnico", 2=>"Graduação", 3=>"Mestrado", 4=>"Doutorado"];

        #tecnicos
        $txt = file_get_contents("./dados/tec.txt");
        $lines = explode("\r\n",$txt);
        foreach($lines as $line){
            if (trim($line) != ""){
                $rw = $this->db->get_where("cursos",["curso"=>$line])->row();
                if ($rw == null){
                    $this->db->insert("cursos",["curso"=>trim($line)]);
                }
            }
        }

        #superiores
        $txt = file_get_contents("./dados/sup.txt");
        $lines = explode("\r\n",$txt);
        foreach($lines as $line){
            if (trim($line) != ""){
                $rw = $this->db->get_where("cursos",["curso"=>$line])->row();
                if ($rw == null){
                    $this->db->insert("cursos",["curso"=>trim($line)]);
                }
            }
        }
        

    }


    function insert_instituicoes(){
        $query = $this->db->query("select count(*) as qtd from instituicoes");
        $result = $query->row_array();

        if ($result["qtd"] == 0){

            $arr = [["IFRN - Apodi","AP"],
                    ["IFRN - Caicó","CA"], 
                    ["IFRN - Canguaretama","CANG"],
                    ["IFRN - Ceará-Mirim","CM"], 
                    ["IFRN - Currais Novos","CN"], 
                    ["IFRN - Ipanguaçu","IP"],
                    ["IFRN - João Câmara","JC"],
                    ["IFRN - Jucurutu","JUC"],
                    ["IFRN - Lajes","LAJ"],
                    ["IFRN - Macau","MC"],
                    ["IFRN - Mossoró","MO"],
                    ["IFRN - Central","CNAT"],
                    ["IFRN - Cidade Alta","CAL"],
                    ["IFRN - Zona Leste (EaD)","ZL"],
                    ["IFRN - Zona Norte","ZN"],
                    ["IFRN - Nova Cruz","NC"],
                    ["IFRN - Parelhas","PAAS"],
                    ["IFRN - Parnamirim","PAR"],
                    ["IFRN - Pau dos Ferros","PF"],
                    ["IFRN - Santa Cruz","SC"],
                    ["IFRN - São Gonçalo do Amarante","SGA"],
                    ["IFRN - São Paulo do Potengi","SPP"],
                    ["UFERSA",null],
                    ["UFRN",null]];

            foreach($arr as $dt){
                $nome = $dt[0];
                $sigla = $dt[1];
                $this->db->query("INSERT INTO instituicoes (instituicao, sigla) VALUES ('$nome','$sigla')");
            }
        }
    }


    function insert_paises(){
        $query = $this->db->query("select count(*) as qtd from paises");
        $result = $query->row_array();

        if ($result["qtd"] == 0){
            $this->db->query("INSERT INTO paises (id, pais) VALUES (1, 'Brasil')");
        }
    }


    function insert_areas(){
        $query = $this->db->query("select count(*) as qtd from areas");
        $result = $query->row_array();

        if ($result["qtd"] == 0){

            /*$arr = ["CIÊNCIAS EXATAS E DA TERRA"=>[
                        "MATEMÁTICA",
                        "PROBABILIDADE E ESTATÍSTICA",
                        "CIÊNCIA DA COMPUTAÇÃO",
                        "ASTRONOMIA",
                        "FÍSICA",
                        "QUÍMICA",
                        "GEOCIÊNCIAS",
                    ],
                    "CIÊNCIAS BIOLÓGICAS"=>[
                        "BIOLOGIA GERAL",
                        "GENÉTICA",
                        "MORFOLOGIA",
                        "FISIOLOGIA",
                        "BIOQUÍMICA",
                        "BIOFÍSICA",
                        "FARMACOLOGIA",
                        "IMUNOLOGIA",
                        "MICROBIOLOGIA",
                        "PARASITOLOGIA",
                        "ECOLOGIA",
                        "OCEANOGRAFIA",
                        "BOTÂNICA",
                        "ZOOLOGIA"
                    ],
                    "ENGENHARIAS"=>[
                        "ENGENHARIA CIVIL",
                        "ENGENHARIA SANITÁRIA",
                        "ENGENHARIA DE TRANSPORTES",
                        "ENGENHARIA DE MINAS",
                        "ENGENHARIA DE MATERIAIS E METALÚRGICA",
                        "ENGENHARIA QUÍMICA",
                        "ENGENHARIA NUCLEAR",
                        "ENGENHARIA MECÂNICA",
                        "ENGENHARIA DE PRODUÇÃO",
                        "ENGENHARIA NAVAL E OCEÂNICA",
                        "ENGENHARIA AEROESPACIAL",
                        "ENGENHARIA ELÉTRICA",
                        "ENGENHARIA BIOMÉDICA"
                    ],
                    "CIÊNCIAS DA SAÚDE"=>[
                        "MEDICINA",
                        "NUTRIÇÃO",
                        "ODONTOLOGIA",
                        "FARMÁCIA",
                        "ENFERMAGEM",
                        "SAÚDE COLETIVA",
                        "EDUCAÇÃO FÍSICA",
                        "FONOAUDIOLOGIA",
                        "FISIOTERAPIA E TERAPIA OCUPACIONAL"
                    ],
                    "CIÊNCIAS AGRÁRIAS"=>[
                        "AGRONOMIA",
                        "RECURSOS FLORESTAIS E ENGENHARIA FLORESTAL",
                        "ENGENHARIA AGRÍCOLA",
                        "ZOOTECNIA",
                        "RECURSOS PESQUEIROS E ENGENHARIA DE PESCA",
                        "MEDICINA VETERINÁRIA",
                        "CIÊNCIA E TECNOLOGIA DE ALIMENTOS"
                    ],
                    "CIÊNCIAS SOCIAIS APLICADAS"=>[
                        "DIREITO",
                        "ADMINISTRAÇÃO",
                        "TURISMO",
                        "ECONOMIA",
                        "ARQUITETURA E URBANISMO",
                        "DESENHO INDUSTRIAL",
                        "PLANEJAMENTO URBANO E REGIONAL",
                        "DEMOGRAFIA",
                        "CIÊNCIA DA INFORMAÇÃO",
                        "MUSEOLOGIA",
                        "COMUNICAÇÃO",
                        "SERVIÇO SOCIAL"
                    ],
                    "CIÊNCIAS HUMANAS"=>[
                        "FILOSOFIA",
                        "TEOLOGIA",
                        "SOCIOLOGIA",
                        "ANTROPOLOGIA",
                        "ARQUEOLOGIA",
                        "HISTÓRIA",
                        "GEOGRAFIA",
                        "PSICOLOGIA",
                        "EDUCAÇÃO",
                        "CIÊNCIA POLÍTICA"
                    ],
                    "LINGUÍSTICA, LETRAS E ARTES"=>[
                        "LINGUÍSTICA",
                        "LETRAS",
                        "ARTES"
                    ],
                    "MULTIDISCIPLINAR"=>[
                        "INTERDISCIPLINAR",
                        "ENSINO",
                        "MATERIAIS",
                        "BIOTECNOLOGIA",
                        "CIÊNCIAS AMBIENTAIS"
                    ]
                ];*/

                $arr = ["Ciências humanas e sociais aplicadas"=>[
                    "Administração",
                    "Arquitetura e Urbanismo",
                    "Artes",
                    "Comunicação",
                    "Direito",
                    "Economia",
                    "Educação",
                    "Filosofia",
                    "Geografia",
                    "História",
                    "Letras",
                    "Linguística",
                    "Serviço Social",
                    "Sociologia",
                    "Turismo"],
                "Ciências exatas, da terra e biológicas"=>[
                    "Química",
                    "Astronomia",
                    "Biofísica",
                    "Biologia Geral",
                    "Bioquímica",
                    "Botânica",
                    "Ecologia e Meio Ambiente",
                    "Física",
                    "Geociências",
                    "Matemática",
                    "Microbiologia"],
                "Tecnologia da Informação e Engenharias"=>[
                    "Tecnologia da Informação e Engenharias",
                    "Informática",
                    "Engenharia Civil",
                    "Engenharia de Materiais e Metalúrgica",
                    "Engenharia de Minas",
                    "Engenharia de Produção",
                    "Engenharia Elétrica ou Computação",
                    "Engenharia Mecânica",
                    "Engenharia Química",
                    "Engenharia Sanitária"]
                ];


            $id = 1;
            foreach($arr as $garea=>$areas){
                $garea = mb_convert_case($garea, MB_CASE_TITLE, "UTF-8");
                $this->db->query("INSERT INTO grandes_areas (id,grande_area) VALUES ($id, '$garea')");

                foreach($areas as $area){
                    $area = mb_convert_case($area, MB_CASE_TITLE, "UTF-8");
                    $this->db->query("INSERT INTO areas (idgrandearea, area) VALUES ($id, '$area')");
                }

                $id++;
            }
        }
    }
    


    function insert_usuarios(){
        $query = $this->db->query("select count(*) as qtd from usuarios");
        $result = $query->row_array();

        if ($result["qtd"] == 0){
            #senha 123456
            $sql = "INSERT INTO usuarios (nome_completo, nome_social, email, password, nivel, email_confirmado, pago) 
                VALUES ('Admin', 'Admin', 'admin@ifrn.edu.br', '7c4a8d09ca3762af61e59520943dc26494f8941b', ".NIVEL_ADMIN.", 1, 1)";

            $this->db->query($sql);
        }
    }

    function insert_test_usuarios(){
        $query = $this->db->query("select count(*) as qtd from usuarios");
        $result = $query->row_array();

        if ($result["qtd"] <= 1){

            for($i = 1; $i < 20; $i++){
                #senha 123456
                $sql = "INSERT INTO usuarios (nome_completo, nome_social, email, password, nivel, email_confirmado, pago) 
                    VALUES ('Test $i', 'Test $i', 'test{$i}@ifrn.edu.br', '7c4a8d09ca3762af61e59520943dc26494f8941b', ".NIVEL_EQUIPE.", 1, 1)";
                    $this->db->query($sql);
            }

            $sql = "INSERT INTO usuarios (nome_completo, nome_social, email, password, nivel, email_confirmado, pago) 
                    VALUES ('Equipe', 'Equipe', 'equipe@ifrn.edu.br', '7c4a8d09ca3762af61e59520943dc26494f8941b', ".NIVEL_EQUIPE.", 1, 1)";
                    $this->db->query($sql);

            $sql = "INSERT INTO usuarios (nome_completo, nome_social, email, password, nivel, email_confirmado, pago) 
                    VALUES ('Revisor', 'Revisor', 'revisor@ifrn.edu.br', '7c4a8d09ca3762af61e59520943dc26494f8941b', ".NIVEL_AVALIADOR.", 1, 1)";
                    $this->db->query($sql);

            $sql = "INSERT INTO usuarios (nome_completo, nome_social, email, password, nivel, email_confirmado, pago) 
                    VALUES ('Aluno', 'Aluno', 'aluno@ifrn.edu.br', '7c4a8d09ca3762af61e59520943dc26494f8941b', ".NIVEL_PARTICIPANTE.", 1, 1)";
                    $this->db->query($sql);
            
        }
    }
    

    
}


CREATE TABLE tb_segnalazioni (

    id_sg INT NOT NULL AUTO_INCREMENT PRIMARY KEY

    ,chiave varchar(40) --20170311|S123|2382
    ,data date --11/03/2017
    ,origine varchar(10) --S123
    ,treno varchar(10) --2382
    ,hit_tot int(3)
    ,in_time timestamp
    ,up_time timestamp
    
    ,percorso text --json text
    ,in_line varchar(5) --2382
    
);

ALTER TABLE `tb_segnalazioni` ADD in_line varchar(5);  

CREATE TABLE tb_estratti (

    id_es INT NOT NULL AUTO_INCREMENT PRIMARY KEY

    ,chiave varchar(40) --20170311|S123|2382

    ,orig_cod varchar(10) --"idOrigine":"S08409"
    ,orig_desc varchar(20) --"origine":"ROMA TERMINI"
    ,orig_time timestamp
	--$jsonOrigOra = convert_date ( $jsonData ['orarioPartenzaZero'] );
	
    ,dest_cod varchar(10) --"idDestinazione":"S08640"
    ,dest_desc varchar(20) --"destinazione":"FORMIA-GAETA"
    ,dest_time timestamp
	--$jsonDestOra = convert_date ( $jsonData ['orarioArrivoZero'] );
	
    ,stop_desc varchar(20) --"destinazione":"FORMIA-GAETA"
    ,stop_time timestamp
	--$jsonInterStaz = $jsonData ['stazioneUltimoRilevamento'];
	--$jsonInterOra = convert_date ( $jsonData ['oraUltimoRilevamento'] );
	
    ,ritardo int(4)--"ritardo":1
    
);

ALTER TABLE `tb_estratti` ADD orig_time timestamp, dest_time timestamp;  
ALTER TABLE `tb_estratti` ADD stop_desc varchar(20), stop_time timestamp;  

CREATE TABLE tb_note (

    id_nt INT NOT NULL AUTO_INCREMENT PRIMARY KEY

    ,chiave varchar(40) --20170311|S123|2382
    ,hit_num int(3)
    ,nota varchar(100) --text
    ,in_time timestamp
    
);

CREATE TABLE tb_stazioni (

    id_st INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    
    ,codice varchar(10) 
    ,nome varchar(20)
    
);

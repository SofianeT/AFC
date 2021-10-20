CREATE TABLE COMPTABLE (
  CPT_NUM smallint NOT NULL identity,
  CPT_NOM varchar(30) NOT NULL,
  CPT_PRENOM varchar(30) NOT NULL, 
  CPT_LOGIN varchar(20) NOT NULL,
  CPT_MDP varchar(20) NOT NULL,
  PRIMARY KEY (CPT_NUM)
)

insert into COMPTABLE(CPT_NOM, CPT_PRENOM, CPT_LOGIN, CPT_MDP)
values ('BRAEM', 'Julien', 'jbraem', 'jbr'),
       ('BLAIS', 'Murielle', 'mblais', 'mbl'),
       ('CHARANSOL', 'Michaël', 'mcharansol', 'mch'), 
       ('CHAZEL', 'Thomas', 'tchazel', 'tchz'),
       ('GONZALEZ', 'Anaïs', 'agonzalez', 'agon'),
       ('GRUWIER', 'Nicolas', 'ngruwier', 'ngru'),
       ('LEBOURG', 'Laurent', 'llebourg', 'lleb'),
       ('POUCHOULOU', 'Aurélie', 'apouchoulou', 'apou'),
       ('RIMASSON', 'Mickael', 'mrimasson', 'mrim'),
       ('ROUSSEL', 'Renan', 'rroussel', 'rrou'),
       ('USCLAT', 'Louise', 'lusclat', 'lus');



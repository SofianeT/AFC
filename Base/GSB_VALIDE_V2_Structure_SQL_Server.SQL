/*
 ----------------------------------------------------------------------------
             Génération d'une base de données pour
                        SQL Server 2005
                       (16/5/2019 18:33:53)
 ----------------------------------------------------------------------------
     Nom de la base : GSB_VALIDE_V2
     Projet : AFC_V2
     Auteur : BTS SIO
     Date de dernière modification : 16/5/2019 17:15:59
 ----------------------------------------------------------------------------
*/

drop database GSB_VALIDE_V2
go

/* -----------------------------------------------------------------------------
      OUVERTURE DE LA BASE GSB_VALIDE_V2
----------------------------------------------------------------------------- */

create database GSB_VALIDE_V2
go

use GSB_VALIDE_V2
go



/* -----------------------------------------------------------------------------
      TABLE : ETAT_FICHE_FRAIS
----------------------------------------------------------------------------- */

create table ETAT_FICHE_FRAIS
  (
     EFF_ID char(2)  not null  ,
     EFF_LIBELLE varchar(30)  not null  
     ,
     constraint PK_ETAT_FICHE_FRAIS primary key (EFF_ID)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : FICHE_FRAIS
----------------------------------------------------------------------------- */

create table FICHE_FRAIS
  (
     VIS_ID char(4)  not null  ,
     FICHE_MOIS char(6)  not null  ,
     EFF_ID char(2)  not null  ,
     FICHE_NB_JUSTIFICATIFS tinyint  null  ,
     FICHE_MONTANT_VALIDE decimal(7,2)  null  ,
     FICHE_DATE_DERNIERE_MODIF date  null  
     ,
     constraint PK_FICHE_FRAIS primary key (VIS_ID, FICHE_MOIS)
  ) 
go



/*      INDEX DE FICHE_FRAIS      */



/* -----------------------------------------------------------------------------
      TABLE : VISITEUR
----------------------------------------------------------------------------- */

create table VISITEUR
  (
     VIS_ID char(4)  not null  ,
     VIS_NOM varchar(30)  not null  ,
     VIS_PRENOM varchar(30)  not null  ,
     VIS_LOGIN varchar(20)  not null  ,
     VIS_MDP varchar(20)  not null  ,
     VIS_ADRESSE varchar(30)  not null  ,
     VIS_CP char(5)  not null  ,
     VIS_VILLE varchar(30)  not null  ,
     VIS_DATE_EMBAUCHE date  not null  
     ,
     constraint PK_VISITEUR primary key (VIS_ID)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : LIGNE_FRAIS_HORS_FORFAIT
----------------------------------------------------------------------------- */

create table LIGNE_FRAIS_HORS_FORFAIT
  (
     VIS_ID char(4)  not null  ,
     FICHE_MOIS char(6)  not null  ,
     FRAIS_NUM tinyint  not null  ,
     LFHF_LIBELLE varchar(100)  not null  ,
     LFHF_DATE date not null  ,
     LFHF_MONTANT decimal(10,2)  not null  
     ,
     constraint PK_LIGNE_FRAIS_HORS_FORFAIT primary key (VIS_ID, FICHE_MOIS, FRAIS_NUM)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : CATEGORIE_FRAIS_FORFAITISE
----------------------------------------------------------------------------- */

create table CATEGORIE_FRAIS_FORFAITISE
  (
     CFF_ID char(3)  not null  ,
     CFF_LIBELLE varchar(20)  not null  ,
     CFF_MONTANT decimal(5,2)  not null  
     ,
     constraint PK_CATEGORIE_FRAIS_FORFAITISE primary key (CFF_ID)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : LIGNE_FRAIS_FORFAITISE
----------------------------------------------------------------------------- */

create table LIGNE_FRAIS_FORFAITISE
  (
     VIS_ID char(4)  not null  ,
     FICHE_MOIS char(6)  not null  ,
     FRAIS_NUM tinyint  not null  ,
     CFF_ID char(3)  not null  ,
     LFF_QTE tinyint  not null 
      DEFAULT 1 
     ,
     constraint PK_LIGNE_FRAIS_FORFAITISE primary key (VIS_ID, FICHE_MOIS, FRAIS_NUM)
  ) 
go



/*      INDEX DE LIGNE_FRAIS_FORFAITISE      */



/* -----------------------------------------------------------------------------
      REFERENCES SUR LES TABLES
----------------------------------------------------------------------------- */



alter table FICHE_FRAIS 
     add constraint FK_FICHE_FRAIS_VISITEUR foreign key (VIS_ID) 
               references VISITEUR (VIS_ID)
go




alter table FICHE_FRAIS 
     add constraint FK_FICHE_FRAIS_ETAT_FICHE_FRAIS foreign key (EFF_ID) 
               references ETAT_FICHE_FRAIS (EFF_ID)
go




alter table LIGNE_FRAIS_HORS_FORFAIT 
     add constraint FK_LIGNE_FRAIS_HORS_FORFAIT_FICHE_FRAIS foreign key (VIS_ID, FICHE_MOIS) 
               references FICHE_FRAIS (VIS_ID, FICHE_MOIS)
go




alter table LIGNE_FRAIS_FORFAITISE 
     add constraint FK_LIGNE_FRAIS_FORFAITISE_CATEGORIE_FRAIS_FORFAITISE foreign key (CFF_ID) 
               references CATEGORIE_FRAIS_FORFAITISE (CFF_ID)
go




alter table LIGNE_FRAIS_FORFAITISE 
     add constraint FK_LIGNE_FRAIS_FORFAITISE_FICHE_FRAIS foreign key (VIS_ID, FICHE_MOIS) 
               references FICHE_FRAIS (VIS_ID, FICHE_MOIS)
go




/*
 -----------------------------------------------------------------------------
               FIN DE GENERATION
 -----------------------------------------------------------------------------
*/
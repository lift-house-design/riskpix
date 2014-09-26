alter table user add column estimator_data varchar(500) not null default '';

alter table claims add column estimator int(11) unsigned not null default 0;

alter table claims modify column status enum('New','Processing','Complete','Expired','Pending Estimate') not null default 'New';

alter table claims add column street_address varchar(100) not null default '';

alter table claims add column zip int(5) unsigned not null default 0;

alter table claims add column replacement_cost decimal(50,2) unsigned not null default 0;

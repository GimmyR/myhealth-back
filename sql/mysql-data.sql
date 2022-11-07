# ACCOUNT DATA
INSERT INTO `Account`(`firstname`, `lastname`, `email`, `password`, `status`) VALUES('Gimmy', 'Razafimbelo', 'gimmyarazafimbelo2@gmail.com', 'mdpDeGimmy', 1);

# OVERSIGHT DATA
INSERT INTO `Oversight`(`accountId`, `date`, `title`, `status`) VALUES(1, '2022-08-15 19:00:00', 'Syndrome Néphrotique', 1);

# PARAMETER DATA
INSERT INTO `Parameter`(`oversightId`, `name`, `unit`, `status`) VALUES(1, 'Tension Artérielle', null, 1),
																	   (1, 'Poids', 'Kg', 1),
                                                                       (1, 'Température', '°C', 1),
                                                                       (1, 'Diurèse', 'ml', 1),
                                                                       (1, 'Eau', 'ml', 1);
                                                                       
# OVERSIGHT ENTRY DATA
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(1, 1, '2022-10-28 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(2, 1, '2022-10-29 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(3, 1, '2022-10-30 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(4, 1, '2022-10-31 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(5, 1, '2022-11-01 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(6, 1, '2022-11-02 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(7, 1, '2022-11-03 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(8, 1, '2022-11-04 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(9, 1, '2022-11-05 06:00:00', 1);
INSERT INTO `OversightEntry`(`id`, `oversightId`, `date`, `status`) VALUES(10, 1, '2022-11-06 06:00:00', 1);

# ENTRY DETAIL DATA
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(1, 1, 91, 1),
																			 (1, 2, 92, 1),
                                                                             (1, 3, 36.3, 1),
                                                                             (1, 4, 1200, 1),
                                                                             (1, 5, 1000, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(2, 1, 105, 1),
																			 (2, 2, 87, 1),
                                                                             (2, 3, 36.3, 1),
                                                                             (2, 4, 1750, 1),
                                                                             (2, 5, 800, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(3, 1, 103, 1),
																			 (3, 2, 88, 1),
                                                                             (3, 3, 36.3, 1),
                                                                             (3, 4, 2500, 1),
                                                                             (3, 5, 1000, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(4, 1, 132, 1),
																			 (4, 2, 87, 1),
                                                                             (4, 3, 35.8, 1),
                                                                             (4, 4, 500, 1),
                                                                             (4, 5, 400, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(5, 1, 88, 1),
																			 (5, 2, 85, 1),
                                                                             (5, 3, 35.6, 1),
                                                                             (5, 4, 1300, 1),
                                                                             (5, 5, 1000, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(6, 1, 102, 1),
																			 (6, 2, 87, 1),
                                                                             (6, 3, 36.4, 1),
                                                                             (6, 4, 2200, 1),
                                                                             (6, 5, 750, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(7, 1, 98, 1),
																			 (7, 2, 85, 1),
                                                                             (7, 3, 36.3, 1),
                                                                             (7, 4, 2000, 1),
                                                                             (7, 5, 800, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(8, 1, 113, 1),
																			 (8, 2, 85, 1),
                                                                             (8, 3, 36.7, 1),
                                                                             (8, 4, 1700, 1),
                                                                             (8, 5, 1000, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(9, 1, 100, 1),
																			 (9, 2, 87, 1),
                                                                             (9, 3, 36.4, 1),
                                                                             (9, 4, 1500, 1),
                                                                             (9, 5, 1000, 1);
INSERT INTO `EntryDetail`(`entryId`, `parameterId`, `value`, `status`) VALUES(10, 1, 99, 1),
																			 (10, 2, 85, 1),
                                                                             (10, 3, 36.4, 1),
                                                                             (10, 4, 2100, 1),
                                                                             (10, 5, 1000, 1);
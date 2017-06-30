<img width="145px" src="https://nxfifteen.me.uk/wp-content/uploads/2016/01/logo.png" />

# NxFITNESS Core

_Use the Fitbit API to cache user stats and allow queries returned over JSON. An up-to-date [change log](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/change-log) is also available on the wiki, as well as an [extended change log](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/extended-change-log) - with everything since the down of time_

## v0.0.4.0 242a08b6c30cf1ebaac04bdb7ee6de100d0bb640...HEAD ( Thu Jun 29 2017 20:04:00 GMT+0100 (BST) )



## Bug Fixes
  - **acc_del**
    - Renamed table function names to remove table prefix ([433ea5bc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/433ea5bcd85727801e171b0af84da933ab7bda45)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Included users cache files in deletion ([35e5feb7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/35e5feb7087324734fddf2b7d00762728ab6fd1e)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)

  - **api_cooldown**
    - Excluded Nomie and Habitica calls from users global cooldown ([79d5d5b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79d5d5b19aec2fb4f15a79e7e1f9a1bbc4324e2d)) , Closes: [#244](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/244)

  - **auth**
    - Prevented unauthorised access generating cache files ([ad431669](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ad431669d0e6a91605852150c668d86b3fb692f2)) 

  - **cache**
    - Aged cache based on enviroment ([c077c33a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c077c33a2c457813c186f07f047185f27faa3c81)) 

  - **ci**
    - Generalised script ([ab594cb5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab594cb592c7d278da240a498e02c16384908765)) 
    - Added wiki change log generation ([542e1894](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/542e18947162740bf1a53dd027081b5dd52f7957)) 

  - **clacks**
    - GNU Terry Pratchett ([07f6c6ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/07f6c6eabccc57fe657fe6239195b7ca2f24f97c)) , Closes: [#197](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/197)

  - **config**
    - Config path ([5fb1dada](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5fb1dada427923d47f534b1ee60c39ed729e7d9b)) 
    - Moved config files to separate directory ([10201ea7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/10201ea7ed973f109fad356e2bd213defcdc80f5)) 

  - **cookies**
    - Added some debug lines to see whats happening during user logins ([934d12dc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/934d12dc31c3b7b82af8c1d5147ebf308030d452)) , Closes: [#205](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/205)

  - **cron**
    - Removed cron debug ([c5badb60](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c5badb60949a1dcdf72d2e291c0d7aeedaee0e0c)) 

  - **cs**
    - Automatically reformated all code ([0ede471c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ede471c9754bc6be55a1f91f8049eb11a19e38d)) , Closes: [#252](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/252)
    - Updated formatting ([702b6e1c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/702b6e1c14e0a11d3432dcaf1d4f35ae0b03595b)) 
    - Formatted Code ([288699a1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/288699a173b23a03ac45e087c0a21c1270df32ca)) 

  - **database**
    - Fixed where statement ([f83b39f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f83b39f1d10f452d19775323a40adbe902cc60a8)) , Closes: [#198](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/198)
    - Fixed join statment in database ([a4dae6e4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a4dae6e40f77e2d267c6c4ffdedbd6990e74c3e1)) , Closes: [#203](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/203)

  - **db**
    - Added table relationships ([79c760ba](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79c760ba399ab3992f7e56fb7761bbd9c28e0420)) 

  - **debug**
    - Removed debug ([82b403f6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/82b403f60b1e57c2318a8f62077acd9b1280c470)) 

  - **delivery**
    - Moved minecraft rewards into seperate table ([6e3bd2f7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6e3bd2f7ca368349d62944d3ce65098fcace80aa)) 
    - Added new minecraft delivery tabel ([c7cf9f74](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c7cf9f748c1c8320d559a0ec80becfd08eab7aa1)) 
    - Modulised reward delivery ([043977cf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/043977cf463114818a87390d1dc9822486a98775)) 

  - **dep**
    - Added additional dependency ignore files ([1d0122c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1d0122c7fe3846c3a85f3f2268e089dc9ae1b9b5)) 
    - Completed dependancy updates ([c637d5ba](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c637d5bac09e94152aa8687d4e1011b40679ab0a)) 
    - Added new files for fullcalendar update ([5952e989](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5952e989b96965bf6ae45231fbd1af8d62cca8de)) 
    - Updated oauth2-fitbit to version 1.0.2 ([a2b29a19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a2b29a19117689219ece0f83e869acb6e46c51c0)) , Closes: [#226](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/226)
    - Updated Sentry to version 1.7.0 ([0f716525](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f7165259679233e0bf0786371d5f939f3017f0b)) , Closes: [#228](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/228)
    - Updated medoo to version 1.4.4 ([f188b15c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f188b15c42cb73eb61f17063a1c50a12c4f04a71)) , Closes: [#227](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/227)
    - Updated leaflet markercluster to version 1.0.5 ([b15c45d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b15c45d3369f25282c2a291d080d55271b41b1fc)) , Closes: [#221](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/221)
    - Updated raven.js to version 3.16.0 ([d04d4edc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d04d4edc2f2c8edd3c8c18bf6b985a1f9dd86a76)) , Closes: [#223](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/223)
    - Updated mapbox to version 3.1.1 ([dfc65199](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dfc6519980a4c52921b8d64fa1ed51ab817c26ef)) , Closes: [#225](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/225)
    - Updated fullcalendar to version 3.4.0 ([32fc7d07](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/32fc7d07b2e81773b9a818e71bca2f4d527daea8)) , Closes: [#224](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/224)
    - Updated chart.js to version 2.6.0 ([142423f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/142423f33c2d197044f9d0e6382c735fa5cd61d4)) , Closes: [#222](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/222)

  - **dep_update**
    - Updated Medoo database framework ([e8a69148](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e8a691480bb962b2cefeea8f2ba3909ff2019421)) , Closes: [#196](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/196)

  - **deps**
    - Updated bower config ([9517cc54](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9517cc543b4e126ce0ba1a3293ca99734a5bcc06)) 

  - **dev**
    - Updated upgrade function translator ([1e1a0939](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1e1a093967ad4d4b44181b41ce68633aa87ce2ba)) 

  - **docblock**
    - Fixed broken docblocks ([ec11ded9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ec11ded90bd7346d5add7a79909fc39cae054425)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Updated docblock and removed depreciated method ([d209d308](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d209d30822edde16d32c667081a7417faba361db)) 
    - Added description to DocBlocks ([7e1ded3d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7e1ded3de7242fc739cb217acc175283c56a690c)) 
    - Added description to DocBlocks ([2743d7ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2743d7acd24bc23f224b04d35039c297c76f6471)) , Closes: [#249](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/249)
    - Added description to DocBlocks ([5b28728e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5b28728ef6000a6ffeba1dfb994fe01086aee41b)) , Closes: [#249](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/249)
    - Added missing DocBlocks ([e5a346b0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e5a346b0cbb75d4027c8f4cca22190216f963559)) , Closes: [#248](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/248)

  - **fitbit**
    - Handled Access token expired errors ([bc2c6a16](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bc2c6a16df02d973e804004035afaab46deeae1b)) , Closes: [#256](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/256)
    - Added a day to the current streak count, too count the first day after all ([0e2aacdd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0e2aacdddcd399ed17f7178ea89938f00ec1d940)) 
    - Add checklist for each day to beat ([ed75acce](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed75acce0895e0db1bf375a45b43bb5b21b7c138)) 
    - Return task ID when already found ([f83ea80a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f83ea80acd2e5c98044fefe61a0952885c2ebe82)) 
    - Added friendly dates ([0ce62349](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ce623498346e28e938c3502db9fad82e7d11b3d)) 
    - Updated last streak progress bar ([e33723f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e33723f9122d3665eb69f7afdeb8cf732ef368b6)) 
    - Fixed streak length ([64204bfc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/64204bfc7cf30112ca4dd2f6c1efe14804bff6ab)) 

  - **fitbit_api**
    - Added support for new error return to all pullBabel calls ([9dd2b5a3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9dd2b5a3c73c8347d255f5dd7a0438eeb78b8363)) , Closes: [#230](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/230)
    - Added new error code for API errors ([a827d41e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a827d41eeaca5737652464593c57feb2ea557b18)) , Closes: [#230](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/230)
    - Return null after API errors ([ab06b119](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab06b119e42be540746d578d25a3eece3bc00932)) , Closes: [#230](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/230)

  - **fitbit_profile**
    - Replace unused definition with 'POST' string ([d8ae35f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d8ae35f19b3e60e016d2b759184a03916f8fc8ac)) , Closes: [#232](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/232)

  - **gaming**
    - Removed bold text from inbox ([70253b63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/70253b63827dfc9b61440ac1232f3c43a051c576)) 
    - Removed bold text from inbox ([33b5d6e5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/33b5d6e5180d555a92842f10ab23fb46c70fe019)) 
    - Cleaned up reward return ([5a167da0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5a167da0f842db28caae386055125906e63d0774)) 
    - Seperated reward JSON and reward array ([8cd20b98](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8cd20b9894bd601119d7216c1b2b190ea2c08714)) 
    - Fixed XP awards ([6221f625](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6221f625c593aae6e3c23604b7ccb27b89ea336a)) 
    - Fixed reincluding files ([6c4659ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6c4659ac30a297a9074ece760b335b77aaaa2ca0)) 
    - Fixed config path ([05f6ef2a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/05f6ef2af6c94447bc019db711cfabec670874ad)) 
    - Tweaked rules and reporting ([27ca12dc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/27ca12dcbc27f9314db568eb67c1830595125126)) 
    - changed body addition to bold ([8da7c9a2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8da7c9a2e84b468332a149eab85af7e11bfb519a)) 
    - Reduced develop json cache ([553f9962](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/553f99627ae9132aa0acabf314f25acbb3d2e64c)) 
    - Changed reward seperator ([e0d1e4d7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e0d1e4d7449cf30c382c71c5e3078f8346b10345)) 
    - Cleaned up rewared description ([c140819f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c140819f1dca21e7a96eb7203caf07ef15742eb2)) 
    - Return rewards as array ([50f4858b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/50f4858b6527c36d1433a3b1445ac949facc5c0f)) 
    - Max level at 100 ([a4ebeba0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a4ebeba0ad76db791401adaf7ef57454719cda14)) 
    - Commented out test reward ([eb3f0d82](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb3f0d828e44d7220e665e042294cae0c9164344)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added in support for processing file based rewards ([865f6ce4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/865f6ce472455be1e52e1b29c41ce5be4b4eaeb6)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added support for checking rewards in config file ([024c65a4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/024c65a44f22f00046291b716fe95e75044a6d2f)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Removed rewardKey random seed ([55369845](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/553698453a85ff7e1825b86f8bf24f7a1d4b8265)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated gaming to support database JSON input ([f51d4271](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f51d42711650c64c966602956b68777a4bc8a0b7)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP to user gaming class - awarding health points for water within goals ([d077bfb0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d077bfb0b16803cc18cad4b473405e2c84e1c579)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP to user gaming class - awarding health points for food within goals ([04a4f4c0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/04a4f4c0c5c85340cb46aa7e323111d8de0ba7d7)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP to user gaming class ([efa94096](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/efa94096e6de17e02b4cee8de9867edb5ccd3031)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP to user gaming class and guess skill from DB ([e036fba7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e036fba766129ce1de10b29771b36b8ffec63f44)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Used health as variable when calculating effect ([793674b9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/793674b93837e3b141b7659350695d6982cc50af)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added XP leveling to gaming class ([f5bd402a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f5bd402a9be43919cf1c19d8168370822ed7b5e7)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Get level and precentage from database ([9fb691f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9fb691f8a62bb80ac6cdb682b71eb5d16e8c6817)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Bumped version ([51c305c8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51c305c813996e7d7ec3a037e8b984d2f7333907)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added user level and precentage to next level columns ([12d35702](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/12d3570270735de5641b889fea49d10546e0aac4)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added class + level icon to xp return ([acf240a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/acf240a7eed367c04dfa70c0f21bcc391c69427e)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added class icon to tasker return ([c4180f6a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c4180f6a07314cda4b25cfdb61566977b119bb8c)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Included fulling gaming info in return ([02b0f5e4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/02b0f5e4d58c41dba3762fcbd3e6c467d802aa14)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Removed random value from rewardKey ([860a0270](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/860a0270610f4475dc12dc88c378a78c3238514f)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated gaming system to use new balancing table filtered on class and skill ([591d5bf2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/591d5bf29fa3f3f7dc03d2583893b7f92405779f)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added skill column ([19eaa13f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/19eaa13fda5c6e4287bb6d662992a1f32d192b79)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added blancing table ([003200d0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/003200d038973b10fa83372c1aa4500e5ab70124)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated target to skill ([eb2201ce](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb2201cebfa69c27ca279f9e7dc3dc4425131a87)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added table prefix to class variable ([099d6065](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/099d6065be24a73bd692077eb03475eb5183c698)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added user class column ([eeb79d98](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eeb79d98cda40ad1ee34a790144e3a6c0a01f75d)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP reward to support array input ([a172ee50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a172ee50dba51ffb5cc7f1b91dbcc0362f3e7d89)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Rehash rewardkey before checking DB same as log award does ([8e00ebd7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8e00ebd720b0a65a457164ff2731c51539899b91)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Updated XP delivery class to Gaming ([df57292c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/df57292cd82f6c165b97d02df0517d50246cbc4f)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Bumped version number ([b87a4173](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b87a417389dcfa4bb03674a19fd068c7db1624b0)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)
    - Added aditonal character fields ([a1574537](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a15745377e1c81d568f158b430502853beab0ff7)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)

  - **git time metric**
    - Ignore Git Time Metric ([6fad60f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6fad60f8be7293f49e022308f95bd965e9455a19)) 

  - **goal_streak**
    - Fixed end of goal detection ([0f4064e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f4064e104139b9de5ae924ec3421f261c5f58e7)) , Closes: [#193](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/193)

  - **habitic**
    - Added special rules for steps reached & added method to return cat goals ([7450ea54](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7450ea54d0c1a3ee62596d2a4796c8fbbf821d6c)) 
    - Added debug to badges ([d3f45f4e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d3f45f4eed71fbcb3e8a32e503de061c1e40a9d6)) 
    - Cleaned up date ([9ff69da9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9ff69da9544229e6a2abbf25378029e93d685042)) 
    - Check water all day, not just yesterdays ([b25dfe92](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b25dfe92a69c1d03a3346453c3faa118637c065d)) 

  - **habitica**
    - Fixed AJAX error ([7347ccb6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7347ccb6f049ccca862988644cada753d5b753ba)) 
    - Gettings users fuid from DB when Habitica ID provided ([508ee02e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/508ee02e5dd80139c815f495419281327beb2213)) , Closes: [#266](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/266)
    - Only add checklist to new items ([ccc4f58a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ccc4f58a37be6f2970ba110dd926ca6fff956735)) 
    - Award when goal or above met ([be29725d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/be29725d1a5d5dae3fa9153f2f566d814d035d1b)) 
    - Limited inbox to 14 items ([ae28ed4c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae28ed4c77baff0519071a29d856eabf9473ac03)) 
    - Updated task icons ([a6689d7c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6689d7c01586da9c59047d0cef647af831a4ae1)) 
    - Added Nomie icons to inbox ([d62cff6d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d62cff6dec6cc55c8175e250c8e6b938dc84c424)) 
    - Updated habitica profile display ([ce57f1db](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ce57f1dba7db1baf4d2e0c3a6a08cb017c824d84)) 
    - Added Added links to habitica and guild page ([337411d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/337411d343b74f4838242533f55b6e7c01f77ff3)) 
    - Added options to signup to Habitica or connect an account ([2a2e7942](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a2e79427bbae239bd75474e702959c57785ec61)) 
    - Completed AJAX habitica account setup ([8741541c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8741541cf51866eb69490f2eeefb4b5d74dcff5d)) 
    - Fixed regex for new inbox items ([37f83ee6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/37f83ee6c7fd5116f147504ca1b4b0d047317c67)) 
    - Resized habitica logo ([421e25a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/421e25a858b575f00219631a6dda44095cf756c3)) 
    - Added habiticia class icons ([a6df0974](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6df0974798cec6b1b10fc8c1fcca0598aa6926a)) 
    - Return full error array when required ([0aa39e53](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0aa39e531999dc699a67e764f9b58d4773889c61)) 
    - Allowed users to sign up for or register habitica ([0b7a104b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0b7a104b8bed0715b23c1e32a5f5799a27986545)) 
    - Added _ to regex ([deda625b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/deda625ba06355c171b2028bf91ed931a63dde4e)) 
    - Removed emojji from inbox ([70975858](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7097585846931892700cb813bd9c7c2a1b589302)) 
    - Reverted water reward to users prefernce ([0a00aadd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0a00aadd4d72679a57a7fa80205ba44b58b19083)) 
    - Updated reward habit names ([49fe5651](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/49fe5651a62d54ba6e787f38bebf5a9bc7557cbe)) 
    - Added rewards for fitbit badges ([a3de8eb6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a3de8eb619b733ab3128ed63f5fa9b43838e9c62)) 
    - updated reward to be range based ([d5e31f07](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d5e31f070d7a066ec297bc9c0296fbcbb2eb5624)) 
    - Fixed use of _GET variable ([380b9b2f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/380b9b2f09a665ef97f9dbe2422066f112e890b4)) 
    - Added additonal output when rewards already completed ([79aa2a68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79aa2a688c81fd90d61781aa7d0404a768050109)) 
    - Return gold and silver values with XP ([88f6ee06](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/88f6ee06ab57617e6a06a3c2b0f4b6042c14edc0)) , Closes: [#208](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/208)
    - Populate gold value ([7a25a91f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7a25a91f739e0eff835b4252bed5994ab16d000f)) , Closes: [#208](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/208)
    - Added new gold column to DB ([7da768be](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7da768beefb94d9b7940f57965fd4bc5bc1129a0)) , Closes: [#208](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/208)
    - Changed bold value from direction to task value ([38a02211](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/38a02211c3e2119576e3c9e89169a415356b8f6e)) 
    - Invited new users to guild ([2cca37d1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2cca37d1db99cb2938f5cde1a1cb6b9ca37c270b)) 
    - Added feature to invite users to guild ([cdfc5fb9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cdfc5fb9745d3f8ed844d49c1f74f981358443a6)) 
    - Added better error reporting ([65916517](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/65916517ee787e982b870d77d411b570470679d0)) 
    - Check events back 24hours ([382b4c19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/382b4c19d041a51e1602a46e24070712c2af8236)) 
    - Added tags ([0fcd8439](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0fcd8439196b9ad0dfcf47607d292768a9518260)) 
    - Removed debug code ([0f826959](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f826959d13a9c1b6a8942fc6b6ff52e0f17503d)) 
    - Avoid cache when creating new tasks ([40560ab8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/40560ab8d066afe685bafc1576783c1a1fe622f9)) 
    - UNDO -- Search for the right type too ([eed0ed08](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eed0ed08eea22c6654ad7de2ffccbe253dcf3b56)) 
    - Search for the right type too ([541e2d35](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/541e2d353fd28d7d867895add467783ad2bb4547)) 
    - Override cache ([9dc73b56](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9dc73b56e4cef4d31d0414d15abb434729a9549e)) 
    - Moved task clean up ([51c74b3f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51c74b3f5471f23bd4eaf07e93829b743c0a108f)) 
    - Added option to override local cache ([5a7e7870](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5a7e787070a6fc6fa6297885cef108facad8f86a)) 
    - Removed debug var_dump ([362783e5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/362783e563a39325f57b5fdf4d90178bff4f3cc6)) 
    - Ignore PNG avatars ([46abe189](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/46abe189c2f85a3cdb3f515adede1f9ef7f99eb5)) 
    - Changed indentation ([fb012a9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fb012a9b8e0ec23880e5351ae6f9881d52f9f1c2)) 
    - Return avatar ([505e07a5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/505e07a57300ce0b9f3f6a771ec07abfc226b6f8)) 
    - Download users avatar ([520b9f95](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/520b9f950e4862e49fdc22d4d103a7377bf9e113)) 
    - Added method to record reward delivery ([51173a8b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51173a8b48288e0e87f32685dcd565b50b5a67f1)) 
    - Changed format of due date ([89f4a6ec](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/89f4a6ec9f13f181284350564f1a88d6d6494231)) 
    - Updated _search function making it public ([5b56746f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5b56746f99a5230db398f3fffae4f1880018085d)) 
    - Query database after updating Nomie events then process that for rewards ([2b0719e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2b0719e159fa470fb7939655b30737be4cdbc412)) 
    - Added alias to create task function ([b881b664](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b881b66424b77f6c016acb7b30d3bebde2e3040d)) 
    - Added extra notes to streak todo's ([8c77ef9f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8c77ef9fb0796f14d71de8b54eb3aa680de22a01)) 
    - Updated score values ([1ca55111](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1ca55111ac2ed6cce77008d5380c000a7942f2d0)) 
    - Added crushed and smashed rules ([7df78107](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7df7810707611b21ef9f608895c4f498e7378484)) 
    - Added check to reward any unspecific activity ([895d9d8a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/895d9d8a661378fb6601283a95aa50e94201c133)) 
    - Dont cache ID's for todo's ([6675e586](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6675e586cdc78e931316c548a22445bcf4277420)) 
    - Removed debug text ([153de1ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/153de1ee0302e9377731fb1e80b5874dd887e78d)) 
    - Stopped double hashing rewardKey's ([2eb32b06](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2eb32b06d9a1355f3d1dbc5d3ea20182a40b5eb2)) 
    - Added search/create for tags ([d97017a1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d97017a1a4304d0e86546f9591b22b2c08bc8670)) 
    - Added tags class storage and corrected json push ([a6444af3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6444af3ef981f507a4f2576fdb0294315fb9c94)) 
    - Removed hardcoded gaming rules ([7466493d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7466493d97ee52bda68ef20c885427a349d01b32)) 
    - Updated difficulty ([14923cb0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/14923cb0c400d7e87f6914d00210004fdc633008)) 
    - Hardcoded water target ([c7f8a3c5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c7f8a3c534c205a14bc878a40022365b80dd70a6)) 
    - Removed hard coded Gaming rewards ([0470d279](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0470d279922879a76cb3253a9e2d16337f338bb1)) 
    - Disabled notifyUser ([fdb3dc54](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fdb3dc54a15e885ed14b26f09f38576beb74b108)) 
    - Finished support for habitica webhook ([f1acf2a6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1acf2a603d3840d3a6ab49176f37b78d8280876)) 
    - Fixed variable mistake, should use _GET since doesnt work on cron ([141eb9ed](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/141eb9ed810101a299259d896b14db4198f51478)) 
    - turned json_decode to associated array ([e2fbd5ff](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e2fbd5ff11e920db7dcc134626576a9f45270687)) 
    - Added habitica trigger to install habits and tasks, and update user XP stats ([6fdbebbc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6fdbebbc25cdfcefcb9741e951b51507505a2ca9)) 
    - removed XP update after completing action (will use webhook instead) ([b02f14b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b02f14b19bb3163203d2d74f637b99e050ce7d3d)) 
    - Added as allowed trigger ([58964e70](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/58964e70cb08f3b3cf127aec71f9ebe4d06dd72e)) 
    - ignore testing file ([3c0cc20a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3c0cc20a2e7d432cf6075c3252f3fd1dcc632ac3)) 
    - Record delivery ([3a54cf27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3a54cf279ac69d93959fbc52d5325f33cff4006d)) 
    - Removed down tick, punishment is failed daily ([173f5734](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/173f5734edd4d588d64ff396dd29d5f87e7c3971)) 
    - Added down ticks ([74f30c60](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/74f30c6042b78ca415a42cd0749a9264fe866159)) 
    - Updated user stats ([1edf264c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1edf264cec0d4901732a7c2174862c23190dd948)) 
    - Added testing access for habitica webhook ([a4e65fa5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a4e65fa54f41b81746a9de6c6d12f02b39c9a88e)) 

  - **ignore**
    - Ignored UX config file ([6aa708a9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6aa708a9e33b3e0560cb25f893d4b1aae49eeaae)) , Closes: [#206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/206)

  - **inbox**
    - Added API limits to inbox ([7e900d34](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7e900d34d66e9ed8d3721b1ab80728f4142bc9ea)) 
    - Used new inbox system for home page inbox ([a78ce22b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a78ce22b0736fa783b351a17f91bd8e8ad23c354)) 

  - **journey**
    - Fixed tasker Journey inclusion ([4e68d947](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4e68d947bf48dbbc56e025d3bf2eb604339a10f4)) , Closes: [#270](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/270)

  - **js**
    - Used API private location return in privacy page ([9895bac5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9895bac5780d07380f1921b6bb3c136ddbaea257)) 
    - Add padlock to private tracks ([bfa3a4a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bfa3a4a7463a829ebf3b1df1a841c1a075d8aabb)) 
    - When geo loaction isnt availble or page isnt secure (Chrome blocks geolocation on insecure pages) centre map on Madras College ([f1f3243c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1f3243ce8dc6d7ca87b2ed7977e1fa9cd3000d8)) 
    - Added map geo points to title bar ([a63cae86](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a63cae869464f49331db974541d4e880bc4d9a43)) 
    - Corrected trending texts ([bdd60714](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bdd607145548f9c38ae1c612f89b75d92a2f2ec8)) 
    - Corrected weight trend colours ([ec49327f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ec49327fb0c0e87ab192b3a0d18f57441161bfa1)) 
    - Fixed longest streak beating value ([65ea1395](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/65ea139582d1a679075b8b39f4ae8d6fc1f3c6ff)) 
    - Removed -'s from 7-day analysis ([a737f8bd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a737f8bd06a1b6b3968fd5b4e9faa098bef4b585)) 

  - **nomie**
    - Dont query nomie if user isnt setup for it ([d2a647aa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d2a647aab6e0495c1fd3d9886bbda3acb9fd0695)) , Closes: [#229](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/229)
    - Fix for some misplaced array elmliments ([bb5f5ee7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bb5f5ee7eb50450929ed67b3cf863f9979a25b00)) 

  - **nomie pull**
    - Added progress bar ([872a5dd7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/872a5dd751eb671bd7417e04c36aea1cb3315ec7)) 

  - **nxr**
    - Removed additional space ([bf59bd02](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf59bd02790fb801808cb58e62daad2e18a7ecb7)) 
    - Fixed indentation ([f1705872](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1705872191205e16d476c466d4db0a67351e0e1)) 

  - **php**
    - Crushed some more divison by zero errors ([701a3444](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/701a3444ab94a7a91318537b71b30110161e1d84)) 

  - **phpunit**
    - Installed phpDocumentor from binary ([406718ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/406718eac59740d7b6d88adca5ca8e4a6b9dc89e)) 
    - Updated paths to ssh keys ([f3d65ed9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3d65ed9220c6aa58eedba4fac45d7e99aa63fb4)) 
    - Removed broken unit tests ([e6e5a6f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6e5a6f969ffffe36398e5df92ac5e9d6f727ebc)) 

  - **privacy**
    - Use new privacy json format ([92f362b9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92f362b9038740b2f537386ace6e0ddc883069fe)) 

  - **return**
    - Added weight loss forcasting graph ([a5c9268d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a5c9268ddffaec7fb5455fca62ddef55c031b968)) 
    - Added returnUserRecordWeightLossForcast function ([01f1900e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/01f1900e48fa3d1ee878a36ed9ecbcc07d60f8e6)) 
    - Removed debug information and set private if start OR end are in circle ([ed3d9844](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed3d98440ccb32eca3962d42f17d7efb8716a021)) 
    - Check for user cookie before returning private GEO posts ([f22f0e87](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f22f0e87720ed03591830d88295a6c18795ffcf3)) 
    - Added return feature for private locations ([7c436701](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7c436701e11dc23621be5626e7612acb7d33e42d)) 

  - **reward**
    - Get WP table prefix from database ([ea8a0dab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ea8a0dab70bed27608c6749b320b8e3312915c9f)) 
    - XP = User WP Balance ([e2711608](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e27116086a74717ba40a6c6274b06ebacf6ce0ba)) 
    - Error proof parameters ([682a1817](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/682a1817a287c4e72a0165b270053b06409ff787)) 
    - Cleaned up run logic ([802a3506](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/802a35066dc99a053f7748f5f0bdd02b5879f91b)) 
    - Check yesterdays steps ([b79969a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b79969a8682aef7f7eb62b7c80c0bbaf8c04fe5f)) 
    - Fixed column name ([ec825c9f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ec825c9ff7ee9c0ef70a8975341bbf0bda04cb63)) 
    - Get yesterdays water ([66c1b790](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/66c1b790f7e8a0198949f76c830ac3f60538200c)) 
    - Scratch that, just check today ([92624eea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92624eead6d34acb82bac3251792e27c27be4f82)) 
    - Check activity from yesterday ([fa2d1f19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fa2d1f19b3bb55000d4d8034c4caf5b21abe25e6)) 
    - Hashed rewardKey before storing in DB ([20cc0243](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/20cc0243352d4f37fd2134edd9ffa1a815e9e428)) 
    - Added bonus points for streak length ([3cd56b3e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3cd56b3ee183aaacbef185c2a32f3867f0e41f37)) 
    - Adjusted triggers to support other types ([2332f486](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2332f48615b9d61ed8a73d2ab966e0d549565117)) 
    - Updated steps rules ([f8f311a5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f8f311a5b7173297fb231e17da4240724cd906f7)) 
    - Updated streak reward rule ([0da6c298](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0da6c298c31a176fce462911814a9aa0020bc0c6)) 
    - Added streak start date ([aad89169](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aad89169bf373672c79d797c1c539fc001413b9b)) 
    - Updated meal rules ([624049d9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/624049d99952eb87bef270eada9e1013c8c61fbd)) 
    - Added water rewarding class ([b601d024](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b601d0248071098560d97b22ebcb634ba866edff)) 
    - Updated new class output ([752f1c9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/752f1c9b4f3c995ee2e774dc708b00a899fee709)) 
    - Updated reward system ([47acf787](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/47acf787a73777b54e1c0cb746097724f6e91f66)) 

  - **reward_nomie**
    - Past more details of the event to the reward system ([f1f27000](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1f27000d3c68737bb973bbbfab1070dbcccfc45)) 

  - **reward_wordpress**
    - Fixed insert rule ([1c95d60e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1c95d60edb207d18337b5d9a7e7d903c7d2cf07a)) 

  - **reward_xp**
    - Added XP delivery class ([743113a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/743113a7f7212d5844b29e45e5009a49ae536e81)) 

  - **rewards**
    - Added function to return rewards by delivery system ([cbea53d1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cbea53d1a57ccf0d77e69cc7836d024d563a9e91)) 
    - Complete write of rewards ([2d40be22](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2d40be22b663b0927be4d39bd2b7cd28dc6ca6bf)) 
    - Made leveling harder ([ee6045ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ee6045ee94e805eae3b728315a1883bc5f87d8ff)) 
    - Ignore current XP images ([92ded2eb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92ded2eb78cb8a5d41c8e35a07572fb207886fe3)) 
    - Added XP reward list to inbox and reward icon to tasker return ([57952781](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5795278142ba74649fb352affd5dd41f709141b7)) 
    - Allowed key id to be null ([478329e5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/478329e51e423a030dc69ea1ba7995edc5138f7b)) 
    - Updated first level end point ([7eaf35b5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7eaf35b52f39228151cd39b42aa4e4df073db2ad)) 
    - Added leveling data returns ([145ee190](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/145ee190c1af9ed4dc33f1be3a1ee52df9e0bf93)) 
    - Only check for hunderth awards on previous day ([0228d5f5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0228d5f52e0c7f0ce510b8134798ccbab204f8ca)) 
    - Code format ([7859f3f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7859f3f9ef0c557a061298b9688236caba2a83cf)) 
    - Awarded XP at point of award ([2a0482d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a0482d45fb0697637c78b7a18360e8680dcd1ce)) 
    - Tightend up Minecraft awards ([f97289ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f97289ee228becf9f5a3206fe99a6b8ca890cfb1)) 
    - Dont create new rewards at all ([22ddaeae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/22ddaeaee812dd58a521cf90e5b7b8a83dbdeca0)) 
    - Delete old awards ([75511b82](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/75511b820262b5e20cb67ada1ca1a7b95b2444f5)) 
    - Only create new awards in dev environments ([5b78fbcd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5b78fbcd1bbf7435a48864149ef5a0488670e72a)) 
    - Added XP awards ([12e4d1e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/12e4d1e1a7370cc93f99de4978adfd82bac721a1)) 
    - Added key's to each reward ([84b9f3f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/84b9f3f2518e281dae9d0f1c9aa4450cf41697de)) 
    - Added reward key items to table, defaults to current date to prevent duplicate rewards ([2aaeb082](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2aaeb082cd7f013e769600c30bd2dd8757eed736)) 

  - **sec**
    - Crap ([ac21ff9e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac21ff9e1785d22ff16dcb22caae555eea70b0b6)) 

  - **streak**
    - Correced steak rules & code format ([193e5a41](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/193e5a41aa30264ee0d3539fc73a001ce63dc54f)) 

  - **tcx**
    - Updated TCX download path ([ae9ac6da](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae9ac6daee1e4028a1b006b7394a6f1a9cf7b16b)) 
    - Removed override visability ([f2c06d58](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f2c06d589bbbeec60e9aecf3b679824b573bf3eb)) 
    - Corrected file path and added support for multiple geo points ([80e495eb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/80e495ebe112faa9994baae1088c021e4c1edadc)) 

  - **todo**
    - Removed considered todo ([35c162e4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/35c162e426ad15ac6fa1791dcd3048883446379b)) 

  - **update**
    - Bumpped version number ([2fca0243](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2fca0243de455976c8447752ed0901ad67372a24)) 

  - **upgrade**
    - Prevented . upgrade interfering ([2e3f36d9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2e3f36d9b63b7d8af6c1c2cfcb456d5806e50d35)) 

  - **ux**
    - Updated index limit ([94c0eb59](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/94c0eb591de56c98065d18e855a322d18c406c4a)) 
    - Updated device layout ([6e330816](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6e33081677a71244e96dcbb9b22a226c5c863e50)) 
    - Mobile layout for fat page ([9246b6ec](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9246b6ec3ea377827ac0f45a85e1e4997fd8b564)) , Closes: [#239](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/239)
    - Mobile layout for weight page ([685efa09](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/685efa099eaeea21ad0faaf265ae46cf1f0a867e)) , Closes: [#238](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/238)
    - Mobile layout for dashboard page ([a93e8b19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a93e8b19580406c54f7eb08b2f87f4db93fb3c73)) , Closes: [#237](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/237)
    - Mobile layout for food page ([2ac78ba3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2ac78ba3e9a567583aac6f20f4211a9d32616910)) , Closes: [#240](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/240)
    - Mobile layout for Nomie dashboard page ([1502959a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1502959afcf367076475bbbce52d28fb9f88e3f2)) , Closes: [#241](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/241)
    - Mobile layout for Accounts page ([74a0d2e0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/74a0d2e0b55da1b6666bacbbb249c78aeb272b5a)) , Closes: [#242](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/242)
    - Renamed simple-line-icons in CSS to avoid conflict with Nomie ([199f8ec5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/199f8ec5dc0851c4abe38c9f9f61ead3cee525af)) 

  - **wp**
    - Updated WP prefix name ([95690413](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9569041397e2723ee73c32b3cad76a281cd7cecd)) 

  - **xp**
    - Prevent negative XP ([6f26e62c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6f26e62ce3e7a78d96216cc3b5040fe50141034a)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Only check water on the day ([8144ad48](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8144ad486a2cab82991c706492d84140f08fe769)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Only check meals on the day ([23c35dc1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/23c35dc19ba8ff23592e39574c2a5f55ee19f5e4)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Only check tracker triggers on the day ([881b4985](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/881b4985447f9825efe421f943abe1ad9f904d94)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Only check body on the day ([484242a0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/484242a0a16dccb9df0b5a8ec0616da7735f4672)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Fixed level looping when XP is 0 or lower ([6604b5f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6604b5f39a7348a0ed3c7e0d67dfccb6242e2c7b)) , Closes: [#207](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/207)
    - Changed level increments ([1a933768](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1a93376818fdeb0c7713e25ba63d8ebe94c02996)) 





## Features
  - feat(nxr):Removed unrequired print_r's ([3144f6e4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3144f6e41fd05f887fe7eb8052b9addcd25baaa1)), Closes: [#267](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/267)
  - **acc_del**
    - Updated cache folders ([605ecfbc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/605ecfbcd8696998b30b63e487645849fbbacb27)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Force user to goto logout page ([2e9c0821](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2e9c08216a76c4d8675a58bdb23ab318536a86dd)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Added functions to remove user from all database tables, including realted items like device charges and sleep logs ([d0bdf9f6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d0bdf9f687563f7217647ead5539ab2f5b5314b6)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Get a list of all database tables, and the user column name for each ([6f53011c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6f53011c97654f5e6d89b6c025bb0465f73dccf8)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Updated UX components to trigger AJAX action ([0bb13b9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0bb13b9b0e01f7a254ea3125481b5f6788c1ed46)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Updated AJAX to identify forms ([602db5cf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/602db5cfc5a8d5490a44bef79d82dc40c74e469e)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Added delete UX elements ([37c8dc94](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/37c8dc9409bfec72d814c09e9b654fb149b36ea7)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Added new account pages ([8ac33424](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8ac334243e99cd20e39af1112a0e31b54f4383bf)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)

  - **account**
    - Allowed users to set which intents they user, update their password, generate a new API key and update the email address. ([91f556ad](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/91f556ad8b5e47a982931ca5f8da72b31c26fef7)) , Closes: [#233](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/233)

  - **auth**
    - Added base function to return users authentication ([96db59ab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/96db59ab2b90a71aacb3132f6b395258b82e86c8)) , Closes: [#243](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/243)
    - Added base function to return users authentication ([328d8ab9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/328d8ab901215e1b3f90336fadbbde7766cdd2fc)) , Closes: [#243](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/243)

  - **ci**
    - Rearranged checks ([7ebca07c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7ebca07cc085e9e3381592c9cf52ff8ccfc03200)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)
    - Failure is not an option ([be29aef9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/be29aef93717436d7d8f092d5e9be99bc081ddd7)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)
    - Added some debug stuff to build ([5a4e39dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5a4e39ddf9aedfe1dbaba6bf29f81f63d18f32d3)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)
    - Removed sys app installation, since I've moved away from docker ([b594df80](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b594df801039d542107785873c4d1f9e9c7ee8f2)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)
    - Fixed filename error ([cd48068c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd48068cb42b9a0122e5540b18a06dde03760067)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)
    - Re-enabled CI ([7649d27e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7649d27ed148312bf80d301a9689a0886dc625cb)) , Closes: [#195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/195)

  - **conv**
    - Added converter function from Runtastic to Fitbit ([0ebef2cc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ebef2cc60a96b816f0478782159ac41353ff0f2)) 

  - **dev**
    - Added shell helper convert version numbers to function names ([41bbdf1c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/41bbdf1c6a3a681e15bfdcdfb189284248ba9468)) 

  - **fitbit**
    - Fixed POST ([a34ec534](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a34ec534d83e4db9ba2d47f26f4b1799d1a6e9aa)) 
    - Added user adjustable steps goal ([4f83bfb3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4f83bfb31614ba2ea99ba1c3f095f185b590833c)) , Closes: [#245](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/245)

  - **habitica**
    - Users can now choose to join quests automatically or do it manually ([c35190f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c35190f84b1cc53320974f79c587a67f0a53ea95)) , Closes: [#269](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/269)
    - Added auto purchasing of Gems ([8d822cd4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8d822cd4691bdcc8ae47d6dd00049f24d309be1b)) , Closes: [#268](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/268)
    - Added new UX settings for Habitica Fun options ([73e35ea0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/73e35ea015aa67d1d6214ac682cbbf17f4d86a70)) , Closes: [#264](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/264)
    - Changed log output ([a2cfcb66](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a2cfcb665b7f3219359469e6d36edf131f2218bf)) , Closes: [#264](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/264)
    - Selling off spare potions ([e7320f36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e7320f367c822c8a005e574a5656b0a3a2c6be14)) , Closes: [#264](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/264)
    - Used built in class for pet type ([25dca45d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/25dca45d3187783b8b5fbebc8126711100ec0cae)) , Closes: [#264](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/264)
    - Added user settings checks ([1f6ce9d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f6ce9d3d94a611d4a952f7baeb0152a1d540940)) , Closes: [#264](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/264)
    - Added some fun to habitica users ([17d65a00](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/17d65a0093c997a4a2e37b11130918a609df466c)) , Closes: [#259](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/259)

  - **nxr**
    - Added support for arrays and objects ([c44fe832](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c44fe832dd88489c46815bcb456016429b85032e)) , Closes: [#267](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/267)

  - **privacy_geo**
    - Fixed privacy to include radious and delete/create cache items ([2f1dd8a4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2f1dd8a4765c856dde61cc18d1ef289ceb5456ad)) , Closes: [#247](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/247)
    - Allowed users to add and remove privacy marks ([164dc942](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/164dc9420215591d8feb03dfcbe8665b38980f64)) , Closes: [#110](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/110)

  - **reward**
    - Reacted to meal quality and trigger new rewards ([b5fb8cd2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b5fb8cd2eb77c0bc31215363d9e5f39734174b64)) 

  - **settings**
    - Completed users journey selection ([f523d21f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f523d21fb78d169f28972c54e618ddee15756569)) , Closes: [#246](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/246)
    - Added journey values into data return ([4424f0b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4424f0b1c84a942bbf48ff606886ef7027fda550)) , Closes: [#246](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/246)

  - **sidebar**
    - Updated sidebar layout ([f9884fd8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f9884fd82624224046a4c5ab9620c70847e8e1ca)) 

  - **ux**
    - Added Journeys ([391eeeac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/391eeeaceaa019a2a2263f2534b06ac53458aaaa)) 
    - Added Pushes ([c2764f2f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c2764f2f88c27caa4c76554a8acfbd377f0f2f64)) 
    - Added push settings ([dc029838](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dc029838f41badbea0d11b0eca1a2549107fba02)) 
    - Changed display when no push active ([38a8f8db](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/38a8f8db00a1086ccc6fc7d62ddf66e8a6e2009d)) 
    - Updated display layout ([450a707a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/450a707aed9328b2bc107f8bd2b2336239ba3bc9)) 
    - Added Push support ([925b1e7c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/925b1e7c333beb14409c3316b9dd48f509ecb33c)) 
    - Added JourneysState support ([5994c6ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5994c6cafa31d81c3bc68336aae3a21c92992b68)) 
    - Added KeyPoints support ([d4eadc71](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d4eadc717bc0a91ede034796fde99ec5733bd248)) 





## Documentation
  - **phpblock**
    - Updated block comment ([24228328](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/242283288dfe803a1ba178fb173652f45065e321)) 
    - Updated param ([675bcedb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/675bcedbc808d8e68aa4fdf9b5892d3b0e600ccd)) 
    - Correctly defined @param variables ([bccee77b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bccee77badd96331438f90716c90001dd09a8600)) 
    - Correctly defined @param variables ([81b70281](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81b702817a670831c6340d645f9aa7f3cade2abf)) 
    - Correctly defined @param variables ([67b7e929](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/67b7e929430e58fe2e5ce79e76cc1e4c1ae30b15)) 
    - Correctly defined @param variables ([2347bea2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2347bea26a50da0a1bfac6d106e35d5afff87e57)) 
    - Correctly defined @param variables ([5c585538](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5c585538d8236508d7265b17979fd9033fa3ef0c)) 
    - Correctly defined @param variables ([2a30910c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a30910cb8dc784f11c111938236ccc1b228dab5)) 





## Refactor
  - **ci**
    - Only run test for develop branches ([8b2eeb1f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8b2eeb1fb910c8114704525258b987a1e915cc80)) 
    - Disabled CI ([37dcda04](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/37dcda04969d2420c708c875019304b52bec0f0b)) 
    - Allow all tests to fail ([8b45be4d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8b45be4ddd73cb5578d697fe48dd3a26b5792078)) 

  - **docblock**
    - Put back blocks, removed by IDE ([cc5e4f6a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cc5e4f6a7d5be80aac5559491c7fd7dd97836b6d)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Put back blocks, removed by IDE ([d2f57651](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d2f576517191a5dbed50f15b3ed77b10dbbe4b6f)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Added missing file summary's ([bf714c36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf714c362b44cf42ec144f5d62fd22a298dd572b)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Updated page summaries (again) ([a44d60a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a44d60a824231fd60784877e7d2870b81c0d735c)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Updated page summaries ([a9906af0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a9906af03ac4d024f6159184d3d7c8b2e4785321)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)
    - Added summaries for constructor methods ([eb64d4ab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb64d4ab51732914fc22ac77817ce936f21c5b9b)) , Closes: [#265](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/265)

  - **errors**
    - Added some more error tags for Fitbit API try failures ([70c69222](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/70c69222bf4acf1694d1a952fabc6932ebf77c25)) 

  - **fitbit**
    - Only trigger fitbit activity based on previous days record ([a1db3ee3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a1db3ee347354aff131c0a3f81be86a42064e3d5)) 

  - **habitica**
    - Updated criteria for healthy meals ([170a9f89](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/170a9f8967fd749f9f04f095ad3739b9ab4e2e24)) 
    - Looking for users habitica creditials in DB ([3d82e44a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d82e44a3e1d02ec0c44dd851044d9dba3ed2fc3)) 
    - Used ENVIRONMENT definition to decide if which habitica server to connect too ([40049027](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/40049027605927f5bf35b9a4609098dbdbaffe8e)) 
    - Used ENVIRONMENT definition to decide if which habitica server to connect too ([e6896f84](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6896f84d264645b3e49ed3a4f38f2b7ae91e7c5)) , Closes: [#263](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/263)
    - Looking for users habitica creditials in DB ([cd648a51](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd648a51a5c79d5f27e5393a0c8ec83940d9bce1)) , Closes: [#262](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/262)
    - Changed config key's ([088a443c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/088a443cc9b05f827d6fcf9ddb1e732ccc3ffa35)) , Closes: [#261](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/261)

  - **ignore**
    - Updated ignore file ([cb8c5bf3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cb8c5bf3f84f2be7a30b32e70773708a4e6f7a3f)) 

  - **phpdoc**
    - Generated missing DocBlocks ([83735087](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/837350872e14ff630da25d9c42894c8092a14027)) 

  - **phpunit**
    - Moved todo ([e0bf3789](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e0bf37893000cc59a666878606a27ca414b0c2c0)) 
    - Updated test stages ([8d470147](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8d4701472300cde1aac2f50afa2ab92a2e022cae)) 
    - Updated test stages ([86c23216](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/86c232163d8715d9c4f4f8a8bce357fd4a81640b)) 
    - Updated test rules ([58157826](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/58157826cbe2eeccc5bd2c4e390cb6b8a860e3f0)) 
    - Updated rsync path ([8aeaf480](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8aeaf4802db49808eaa26b5c00cebf086aeef90f)) 
    - Updated test directory ([a47200e2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a47200e2fce62144072eb41b7886a8ee6e9b508d)) 
    - Added missing phpdoc blocks and TODO statments for all untested methods ([4be219a0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4be219a003b51975b5e4bc0b1ee793eb714103bf)) 
    - Added missing phpdoc blocks and TODO statements for all untested methods ([d4d92f93](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d4d92f935d66ae42d0bc25ac4e3d18375676a484)) 
    - Moved tests to new folder out of lib ([9d1e938f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9d1e938f8b82ef1406afbd2461a5a6ff9df8d2dc)) 

  - **settings**
    - Moved active intents settings into seperate settings page ([50dfbfc1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/50dfbfc1a6e37e76d12483c54404fe06836ec26b)) 

  - **style**
    - Updated code style ([ff0128b5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff0128b512492a71a128106795a42bbbbbb6dd41)) 

  - **update**
    - Updated gendocs tags ([9ca588c0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9ca588c088d2e99b24a31a86cd548411356201a7)) 
    - ignore vender file ([97acf957](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/97acf95738735b92aa3c4387002290213a199f56)) 
    - moved docgen to staging ([0bf7949a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0bf7949abdc82b588ff8e69ad101f4073a0fb252)) 
    - Earmarked database linkage for 2.0.0.0 ([a04b6519](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a04b65194c347e66ea7ee3d69be0dc7feeb81499)) 

  - **ux**
    - Updated icons in sidebar ([63ce32fe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/63ce32fe8eb20713f8368a191091b3cccfe797ba)) 
    - Added blanker space for future journey support ([4bad15f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4bad15f14aac8f32227cda262ba88f8f9a2ced42)) , Closes: [#246](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/246)





## Test
  - **unit test**
    - Updated code coverage ([9b08589d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b08589dcd26a88004e049ef31e6569f60d37f38)) 
    - Moved config tests to one file ([e18c1021](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e18c102134179892c884f00f88c3356818a0783f)) 
    - Moved vender to bundle folder, git ignored DEV packages ([72daa1de](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/72daa1de7d9e848f4eebc3fc0e67d6e02ab3d053)) 
    - Changed site ID ([15236268](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/15236268768062e07d06a4938aaf6011fb79647c)) 
    - Updated copyright in all files ([4caa323d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4caa323d69ae5afb95a5308808eabe8f05f9d347)) 
    - Added new tests, and method to delete from database ([4114965e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4114965eab37b90b6e107f284fef40dcbe803097)) 
    - Added test for config storage in DB and array ([e9164d45](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e9164d45ba53db7145ed68327d38e3c4db939d28)) 
    - Removed autoload require, updated test function names ([4eba5005](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4eba50056e700c5442a03145bf25f324e17add87)) 
    - Added bootstrap ([c3bea2b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c3bea2b425f89e006bc40be6c65c4c6b70f03e98)) 
    - Don't log to console if running in unit test ([5f6e1744](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5f6e1744cdc26b34fab9bf745bdfa63452eb64b0)) 
    - Don't test private functions ([e6f28d6f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6f28d6f25fd0d353fe14a03c56595fe38ecc5ac)) 
    - Added unit test to ErrorRecording ([5c8d4098](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5c8d4098f04de2f3ad3ebdd33e090013d504cded)) 
    - Added unit test to Analytics ([cc626200](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cc626200dba0292b1a8a8a82adb45dd22cf6f0e0)) 





## Chore
  - **badges**
    - README.md ([271df2fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/271df2fc7a483132e98cf712264c91eb6828f447)) 

  - **blank**
    - Added blank getThemeWidgets function to prevent errors ([db61b3f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/db61b3f857494568b6c27db1795ae5f9f144c375)) 

  - **bundle**
    - Updated bundle ([348cfb55](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/348cfb5507ed99f03929ea9a555de1f34cdeb09e)) 
    - Included momment.js ([b8b7d592](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b8b7d592e27061a5ac1ea47db437425d83011176)) 
    - Changed tests ([e4a19d30](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e4a19d3015d6b6b86956c804735542a5ae84a003)) 
    - Updated path to fullcalender.js ([140ae109](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/140ae109f670defb21d889e3dae18f912a47296c)) 
    - Corrected path spelling ([8aa9faec](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8aa9faecae05aff21ec3211cd472fcf7696889b1)) 
    - Ignore bat files ([e8f6164c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e8f6164cc4cc99520ef03139a9e4152520574179)) 
    - Updated include paths ([ed43c085](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed43c085274bc14ee298e19625231734cd16677e)) 
    - Updated bower bundle path ([c3f91778](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c3f91778f8e169411a803f86ec745b423d3b4000)) 
    - Updated review ([ebcfa042](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ebcfa0421e915e6b5126169be1d2cdd871b48a0d)) 
    - Removed check for other bundle location ([fce10b56](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fce10b5625ad13648c44a5940c69fd4442729454)) 
    - Added JS header ([4b36edff](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4b36edff88f4de326785ba82bceb95c52419ba75)) 
    - Moved autoloader ([5070d1ef](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5070d1ef3e7478c4e00972ba2285d3c4a4cd4ef9)) 
    - Reinstalled deps and loaded composer loader in autoload file ([2b546ab6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2b546ab647d4b46c882133c07846ea631f7b05c5)) 
    - Added project composer.json files ([34da6984](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/34da6984c9b690dccaf00cae9b097a49c011485b)) 
    - Added dynamic bundle paths ([302b436d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/302b436dbea6157836a0e99aeb23d82815ea7d6b)) 
    - Stagging checks out current branch ([e7bdc7ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e7bdc7ea46d01b7f78e73e122ff2e60902721201)) 
    - Added bundle ([970c78cc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/970c78ccf7a3ff7c06fde364d1acf41aba8da088)) 
    - Used gitignore to limit included files ([1ad26022](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1ad260222dae251ab45f09fb8ddcb32a8639737f)) 
    - Moved deps to bundle folder ([4f6e3f86](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4f6e3f86ba4fdb20040ff61828411a65257b0075)) 
    - Added initial support for bundled bower files and installed ones ([cd4cae5f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd4cae5ff5662b2a0c4fa6e7a330220c09f502e0)) 

  - **ci**
    - error catching ([cdec2fac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cdec2faca51e4ebdf4d7e150f8f096c690b3694a)) 
    - error catching ([4de4f683](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4de4f683e8d7b710436501d59ccfb454175ef915)) 
    - remove config (DB) tests ([509fb8ff](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/509fb8ff19ba875243a87077b21fb957957e99c3)) 
    - New test use ([0241bfbe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0241bfbeb448067feb6d8b19b969bb846c296194)) 
    - remove dev tag ([89edc4b6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/89edc4b602b10fe7924065644398bb10f324a51c)) 
    - Rewrote autoloader ([af28b266](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/af28b26630510add61b681b38f202ada4d2f7f5e)) 
    - Uncommented install ([015c40ef](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/015c40ef68eb963f9d92a55920eef2690462c8d9)) 
    - Updated dev deps ([ccd2da5a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ccd2da5a7542c9c28c7835acc264156f0f2cae21)) 
    - remove cache ([6bcfe23b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6bcfe23b913e7887d0b22617937ba28993d12f58)) 
    - Bootloader ([a755f7a6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a755f7a6076011eb915575d6b018268cb55b5eca)) 
    - Bootloader ([5651ffd1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5651ffd1d02f2499575025f2cdcdd2eba19ee6a0)) 
    - Fixed install of xdebug ([d55d009b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d55d009b6e2ff1e05526c39de7cf06a108424b2a)) 
    - Fixed install of xdebug ([69dc5ac6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/69dc5ac6b9cceb78e25736aa338e0eb833045bc6)) 
    - Fixed install of xdebug ([972b2d2d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/972b2d2d42822532eb20677db93979917fb3bb2a)) 
    - Changed autoload ([0f405b59](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f405b59b8798cb16a55dda42aab4973519bff6e)) 
    - Added debug ([bde86c2e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bde86c2ef32505faf12744ac83383c2f8518dd92)) 
    - Updated checks for Medoo version 1.4.2 ([b725f18d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b725f18d492db22e17f0f6a9376e8cdd4f7ab1f6)) , Closes: [#196](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/196)
    - Updated GitIgnore ([b6ed3c3b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b6ed3c3b18c42feb65eb950c75c04740c2fbdcf7)) 
    - Updated GitIgnore ([239d79d8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/239d79d89cdc3700e114ca5f742e330276b82fc8)) 
    - Corrected Paths ([d3049acf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d3049acf4eb14fad3dd8ef2d2a0c6581912ef2ab)) 
    - Updated GitIgnore ([19a4b9f5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/19a4b9f51b5e8ca4752c8516fa8015e44115a66c)) 
    - Corrected Paths ([3f5caab5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3f5caab5fe18795bef8962f38b8ce4b8492ff60e)) 
    - CI Update ([e9c1dcb6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e9c1dcb68c8a23e8b7d62bf5e3839ec4bb4cbe57)) 
    - CI Update ([87e98c3e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/87e98c3e45b60c4b3c34203de0d8e798a53aa6b6)) 
    - Updated tests ([3b1c8338](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3b1c8338cde20b34938cba8addb18e80c5b6872c)) 
    - Updated tests ([00c132fe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/00c132fe086d1758558f8ea8eeaa2046c05222d0)) 
    - Updated tests ([af63e96d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/af63e96d6080ff48502792892015473436cf7cc8)) 
    - Updated tests ([1a5ff49f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1a5ff49f12c88e68acf3917d4c0bba206020424c)) 
    - Updated tests ([627a8c9d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/627a8c9dd95197b81187ec1ff27c0dd085490226)) 
    - Updated tests ([bc64358d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bc64358de3c4788de49fc1a6f5acc7f2ed09c51a)) 
    - Updated tests ([018d7d8b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/018d7d8bd8d3b986a5621fce86bc29d2b39bcd1c)) 
    - Updated tests ([59d5f47d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/59d5f47d18a85385f5871bbb80ba62fa769cb239)) 
    - Updated tests ([7a87e7af](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7a87e7afb7a8acf08c16c925176592bf3f50f962)) 
    - Updated tests ([18e50139](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/18e5013957a14c2527795a2197f2361dcea4c508)) 
    - Updated tests ([368a27d0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/368a27d0b523ee38f04bb811f5b65bc8bc07f413)) 
    - Updated tests ([565c6a63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/565c6a636d9807f331ea41d2a7dc796b56310715)) 
    - Updated tests ([bf4fb421](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf4fb421cc3cb0b610b8327df6703ef3b25e4f2d)) 
    - Updated tests ([5392b104](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5392b10419137641428305ca30b820669c53562e)) 
    - Updated tests ([9cb1a45c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9cb1a45ceeeb17b51154308977f5aaa68e338657)) 
    - Updated tests ([b7b8e184](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b7b8e184ae71b2bfc6293c389f13fe00d54ecf28)) 
    - Updated tests ([817d78d8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/817d78d82c3d5f7ec066b3c581dfed5b9b2fa71d)) 
    - Updated tests ([7de1b9c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7de1b9c9b319d6c656465b3e46f586e42b4f25dc)) 
    - Updated tests ([8cb5727a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8cb5727a2a514adfca3f845bbb78403e23f7b8cd)) 

  - **debug**
    - Removed autoload debug ([f085e5bf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f085e5bf67174edb72af9f94b944e08eeda9bc82)) 

  - **denug**
    - Removed unrequired debug code ([7599e47e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7599e47e492f538fdb644d21f2381255d006f786)) 

  - **deps**
    - Updated Composer ([ab729d9a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab729d9aa85335e623a498ed81945d3a2d2586e9)) 

  - **gitlab**
    - Added new dep template ([28f60ec5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/28f60ec5ec2651142aedd0f2bb524711efdd8d55)) 

  - **version_number**
    - Bumped 0.0.3.1 to 0.0.4.0 ([19a88c20](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/19a88c20ff53eefbe55a3d5823dd5754f83a8cb7)) 
    - Bumped 0.0.3.0 to 0.0.3.1 ([9a46b809](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9a46b809993ae5e02712ff8fa07af26a3dc5d4ff)) 
    - Bumped 0.0.2.1 to 0.0.3.0 ([80618e44](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/80618e4494ddbd0cc9500c49ea6f6de0ff4f0035)) 
    - Bumped 0.0.2.0 to 0.0.2.1 ([3a8b62b7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3a8b62b733167399b1dc5b03a0f3d2d74037f9c7)) 
    - Bumped 0.0.1.16 to 0.0.2.0 ([4b9d81fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4b9d81fc3d6b82ccb9b8965e2bbb1f64eb9ff140)) 
    - Bumped 0.0.1.15 to 0.0.1.16 ([580bd9ec](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/580bd9ec4d68ff00d1758208311d79583cab0010)) 
    - Bumped 0.0.1.14 to 0.0.1.15 ([c9066eb0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9066eb072abb8337f12c97717bf9037a632f45f)) 
    - Bumped 0.0.1.13 to 0.0.1.14 ([5849bc6c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5849bc6cee4fb48d2a4768404f3fab262a4ef107)) 
    - Bumped 0.0.1.12 to 0.0.1.13 ([7a6f275a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7a6f275a5341a8d1654de26095ebce78d0ca9ddd)) 
    - Bumped 0.0.1.11 to 0.0.1.12 ([15d74195](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/15d7419509796f13f3d479094b750a83661ddc68)) 
    - Bumped 0.0.1.10 to 0.0.1.11 ([bdcb7b5f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bdcb7b5fb23ecda70df78dad4fcde8f437b5bf1c)) 
    - Bumped 0.0.1.9 to 0.0.1.10 ([1350d11d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1350d11d76e0958610228481beeb686e377832b5)) 
    - Bumped 0.0.1.8 to 0.0.1.9 ([c68acdf4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c68acdf427f12a02e5f0841548041c9c4fd34b40)) 
    - Bumped 0.0.1.7 to 0.0.1.8 ([f99c9b0e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f99c9b0ef21e25c384bf6d60f60237b40d41e180)) 
    - Bumped 0.0.1.6 to 0.0.1.7 ([7ad03faa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7ad03faab59e313e5c4542ce824b21fa3e2942d8)) 
    - Bumped 0.0.1.5 to 0.0.1.6 ([cf3b9b2d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cf3b9b2d99b66e0ff579db3c9c4a9d2bfe147210)) 
    - Bumped 0.0.1.4 to 0.0.1.5 ([356db011](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/356db011d70e39d239ddd1459ffeb6dcf275fb9b)) 
    - Bumped 0.0.1.3 to 0.0.1.4 ([c136d2a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c136d2a7d347a9ee5841c40a5ea596e151e52340)) 
    - Bumped 0.0.1.2 to 0.0.1.3 ([ce577f90](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ce577f90ad47d98a2e59e47cfd011423df3e331e)) 
    - Bumped 0.0.1.1 to 0.0.1.2 ([b28768f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b28768f3a7424d22d2fc1ce808fb7940b1283f22)) 
    - Bumped 0.0.1.0 to 0.0.1.1 ([14af3c89](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/14af3c89d97917b0e03da65aa21f04b7e73758ac)) 





## Branchs merged
  - Merge branch 'feature/issue_230_232' into develop ([c0f874a0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c0f874a081f9bc047df57d41c735d5ddacfe2160))
  - Merge branch 'feature/issue_220' into develop ([82bcb2a3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/82bcb2a3717c1d47af7b8b1545754be848bfeaf7))
  - Merge branch 'feature/UnitTests' into develop ([2cc4f10a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2cc4f10a730f17e58e9fb80c245103df2cedaf6f))
  - Merge branch 'feature/DocBlock' into develop ([4c7bfa5d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4c7bfa5dcd708bcf05454c517a0369b9a515a924))
  - Merge branch 'feature/Check_Dependancy_Versions' into develop ([1fba37f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1fba37f84be17227d9601abc8cad5d48ef0ea29d))
  - Merge branch 'develop' into 'master' ([242a08b6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/242a08b6c30cf1ebaac04bdb7ee6de100d0bb640))
  - Merge branch 'develop' into 'master' ([0afeca3b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0afeca3b7ad435bfae8660cfcd4da10a2accf268))
  - Merge branch 'develop' into 'master' ([9bafd1f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9bafd1f9ba49294d1ab655c06761eec1ae490e5e)), Closes: [#190](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/190)
  - Merge branch 'develop' into 'master' ([ddf0d740](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ddf0d740052919ef2045b7fdb864a89a9f16f157))
  - Merge branch 'develop' into 'master' ([a86681bb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a86681bbc79beab6219508f1964ab5eae9a17e28)), Closes: [#189](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/189)
  - Merge branch 'develop' into 'master' ([53b132e8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/53b132e82f368f87ee95ac1d07065f030a120c4d)), Closes: [#183](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/183)




## Other Commits
  - Added depednacy checking test ([1f9f6f3e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f9f6f3e6eb9ec17989319749ee519525296e362))




---
<sub><sup>*Generated with [git-changelog](https://nxfifteen.me.uk/gitlab/nxfifteen/git-changelog). If you have any problems or suggestions, create an issue.* :) **Thanks** </sub></sup>


<img width="145px" src="https://nxfifteen.me.uk/wp-content/uploads/2016/01/logo.png" />

# NxFITNESS Core

_Use the Fitbit API to cache user stats and allow queries returned over JSON. An up-to-date [change log](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/change-log) is also available on the wiki, as well as an [extended change log](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/extended-change-log) - with everything since the down of time_

## v0.0.1.11 1350d11d76e0958610228481beeb686e377832b5 ( Thu Jun 29 2017 19:27:31 GMT+0100 (BST) )



## Bug Fixes
  - Added phpDoc config files ([89a66a08](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/89a66a08a652934785f5be01275075850b1a1faa)), Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)
  - Removed error reporting when token expired ([56f9ef9e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/56f9ef9ea82db9fa13c38373b8d45f999574c541)), Closes: [#61](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/61)
  - Fixed copy/paste error ([4d966220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4d966220604fcc360d9fbe7b342c35f06d51b877)), Closes: [#60](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/60)
  - Bumped version to 0.0.0.7 by increasing database size ([b3723dac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b3723dac2fdeb8edccd173e78f5eca87d1f56b7a)), Closes: [#59](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/59)
  - Added Charge2 to list of floor and heart rate supported devices ([a615b524](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a615b524410d4fc150f0369e8da2fc360f7e4557)), Closes: [#57](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/57)
  - Moved error reporting further down flow ([20d720f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/20d720f3c066e98a9385c19d89dbf2ecf8bd60cc)), Closes: [#53](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/53)
  - This is correct behaviour so removed error reporting ([7d3d4167](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7d3d416763ce7e3e2b351e947deb70e773bb9009)), Closes: [#56](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/56)
  - Added new stock images ([7820d76d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7820d76d2e5f0fd17c965621e222dca0f44cdb83)), Closes: [#54](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/54)
  - Only report error if running user and folder owner are the same ([005ffe83](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/005ffe83114916b9ee162dd44294d6803b007c38)), Closes: [#52](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/52)
  - Increased item limit to 50 ([269c54b7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/269c54b739bd74669d1b26e8ee800af2522f2fde)), Closes: [#49](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/49)
  - /spend 5m ([1ebbbaf2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1ebbbaf20118fd28a7a42322f2a2533631219df9)), Closes: [#48](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/48)
  - Only counting days when step goal reached. Current day only counted once goal beaten ([5123ae9f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5123ae9f9fe0884e3a4d21ece760ca07c096b9df)), Closes: [#47](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/47)
  - updated sql where clos ([794d2a84](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/794d2a84e11620a70e897f58a13056b4cba81e6a)), Closes: [#46](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/46)
  - Finalised issue. If nothing recored for over 90 days in ether body weight or meals will mark as last run date ([ea323ab3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ea323ab316a1163c41f5e4e9a770b9ee8120ddd2)), Closes: [#44](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/44)
  - Added isset check for goals->weight and goals->fat ([5e79c8d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5e79c8d415118243daccffc4b291f23e6f5bda34)), Closes: [#45](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/45)
  - array value checked ([f8cf5c7a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f8cf5c7ae51519f94627cae8cab087741a1e2631)), Closes: [#40](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/40)
  - array value checked ([990b50ae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/990b50ae047014a5001d84c0b35eb6ecc957d782)), Closes: [#41](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/41)
  - Added 7-day step target progress cacluation ([b6a55d4f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b6a55d4fcda55393cdda8159d7d67f0e06340874)), Closes: [#36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/36)
  - Added new API end point [returnUserRecordTrackerHistoryChart](api-userRecord-TrackerHistoryChart) ([c04f6da8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c04f6da8bcfac14a72d55e36900f1b7a599cdfa9)), Closes: [#35](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/35)
  - Added new API end point [returnUserRecordTrackerHistory](api-userRecord-TrackerHistory) ([f91f6c84](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f91f6c84a166454b0e6682af3078df0697aafcd2)), Closes: [#34](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/34)
  - Returned current weight and body fat, and converted weights into user unit ([fa72006e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fa72006eda6b3768907efcfc45a53918e83a3197)), Closes: [#33](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/33)
  - Created class function to convert weights by user chosen units ([6dc727c6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6dc727c64f2fd60a5ce2385b3ee51f60a110881b)), Closes: [#32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/32)
  - Added logic check for zero values before doing the calPerMinute calculation ([32c6d3f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/32c6d3f916ddda997b2055c59bc64a4389d47742)), Closes: [#31](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/31)
  - Created return for leaderboard items ([1eb39d62](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1eb39d62c449c897a1302066093c17f3dec7f89b)), Closes: [#29](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/29)
  - Check $_SESSION is an array rather than $_SESSION['core_config'], then check that the core_config key exisits ([61e3439d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/61e3439d7058e7724d2733d5cf272878861e9486)), Closes: [#25](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/25)
  - replaced old database name 'goals_calories' with correct name 'food_goals' ([666993ba](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/666993ba18fb2c7642fb1eb6f97bee0cd5e85a97)), Closes: [#24](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/24)
  - Updated time series events when theres been no updated in over 6 months Closed #20 - Updated activity log to support new format Closed #22 - Downloading TCX files + Double check to remove fitbit path from URLs - added by library Closed #16 - Downloading event heart rates Closed #23 - Added user cooldown check to pull requests + Check rate limit headers and blocking when less than 2 + Handling 429 (Rate limit) errors better ([344e1938](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/344e1938f2d0740fb975115b39b9edb109e1806a)), Closes: [#21](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/21)
  - After setting a last run check for cache files and delete them ([08f2bf09](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/08f2bf099844ae2c7ef6e9167d21fae236e95ddf)), Closes: [#35](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/35)
  - Using _GET peramiters to determin cache filename ([541338c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/541338c926b5516aec101de7d800f4f1b085b75e)), Closes: [#36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/36)
  - Added returnUserRecordKeyPoints support ([1141f11f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1141f11fe0cf05d4ad6a7561b59bb341378e2647)), Closes: [#31](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/31)
  - Added new function returnUserRecordAboutMe ([57a9794d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/57a9794d97ba3d4ef1eff7803cb29f9e65f4bde7)), Closes: [#30](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/30)
  - Added new weight and fat loss rates ([67f843fa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/67f843fa6c95e594a6f64b7a7c49b642760d03d7)), Closes: [#25](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/25)
  - Cleaned up moving average to make it more configurable ([79a0e5c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79a0e5c7970008e026c4d1b4e59d0bdb013ac5a2)), Closes: [#29](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/29)
  - Added average calculation ([1af20a19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1af20a19de92630b0dcd59d41ab14ca629995a67)), Closes: [#23](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/23)
  - Added unix time return ([fedf9006](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fedf9006f53041ab454265fe594464038b7b5b98)), Closes: [#22](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/22)
  - and #21 Created function to return food diary ([1eb70062](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1eb70062f667fa8df61fb7215bc618bf3bdb6f0b)), Closes: [#7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/7)
  - Returned device information ([5be14a6b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5be14a6b3c8dd3d23ab2277c0d0cbb4959591ded)), Closes: [#17](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/17)
  - Stopped logging empty heart rate records ([78d5636f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/78d5636f2ff54b4de4b53456ed50ba855072ebfa)), Closes: [#16](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/16)
  - Fixed detection of valid sleep record ([3c6cbc7c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3c6cbc7c9112b4237a2bf852000a677bac03d135)), Closes: [#15](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/15)
  - Added support for all time record based on first seen value for any given user ([34d3c18c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/34d3c18ce245bcb97cc6caeb4e13afc17c72fcc9)), Closes: [#14](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/14)
  - Returned min and max values for weight and fat ([25db6498](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/25db649857161d66e1d3e413e031adc49504bfaa)), Closes: [#13](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/13)
  - Closed #10 Closed #12 Corrected calulation of missing records ([31a39c39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/31a39c39ef3490e65909537517143e67c8121d8c)), Closes: [#9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/9)
  - Check for most recent reading when non present ([2c21ef6a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2c21ef6ab2a0e61e348aab4d3d6d421348bce2f1)), Closes: [#10](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/10)
  - Added url argument to override cache ([24a228b5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/24a228b5fab5ee87442a0beccd5683bb2dd250e6)), Closes: [#11](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/11)
  - Moved goal query. Triggers when fat or weight value recorded ([9847d499](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9847d49970eb997b36cb7a87c5e280ccde81e303)), Closes: [#8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/8)
  - Rounded off weight and fat values ([df2edf27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/df2edf27b24bcbee38877f5e3ffd21ad3ec7186c)), Closes: [#2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/2)
  - Estimated unrecorded weight values base on formula of a straight line ([b23a4b81](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b23a4b81ef17c2977626b056b625d7227c01035a)), Closes: [#1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/1)
  - **acc_del**
    - Renamed table function names to remove table prefix ([433ea5bc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/433ea5bcd85727801e171b0af84da933ab7bda45)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)
    - Included users cache files in deletion ([35e5feb7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/35e5feb7087324734fddf2b7d00762728ab6fd1e)) , Closes: [#220](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/220)

  - **api_cooldown**
    - Excluded Nomie and Habitica calls from users global cooldown ([79d5d5b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79d5d5b19aec2fb4f15a79e7e1f9a1bbc4324e2d)) , Closes: [#244](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/244)

  - **auth**
    - Prevented unauthorised access generating cache files ([ad431669](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ad431669d0e6a91605852150c668d86b3fb692f2)) 

  - **auto_upgrade**
    - Added version checking ([d447bd32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d447bd328fc49021e1e736566fa8ad6186ba402e)) , Closes: [#64](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/64)

  - **cache**
    - Aged cache based on enviroment ([c077c33a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c077c33a2c457813c186f07f047185f27faa3c81)) 

  - **camel_caps**
    - Update method name ([a21665c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a21665c7d87bdfe8046ea7ef48dcbe0a2e133dfa)) , Closes: [#183](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/183)
    - Update method name ([75182f4f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/75182f4fa5a631ac2c17b25f7a5e98b7c60c2c0a)) , Closes: [#182](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/182)
    - Update method name ([11c674ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/11c674ee99e3579ae54596342a5a4058a4007480)) , Closes: [#181](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/181)
    - Update method name ([4d97fa52](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4d97fa52886bb3fec062f7e556661cfcc2814552)) , Closes: [#180](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/180)
    - Update method name ([2fd7aea7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2fd7aea7df797c9a9b836ecde964b50075ceefa1)) , Closes: [#179](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/179)
    - Update method name ([3e2b7fa5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3e2b7fa5bd48447534ef52b288f67520eb8f5fee)) , Closes: [#178](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/178)
    - Update method name ([d72a5ec1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d72a5ec14b9bf2196a11b2bbb8baf010be8a35cb)) , Closes: [#177](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/177)
    - Update method name ([99bf2e54](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/99bf2e5476487fd8da96ae12482762b96fc6b279)) , Closes: [#176](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/176)
    - Update method name ([ba8433be](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ba8433be6192e7aeb77f590ef80e5491c489100b)) , Closes: [#175](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/175)
    - Update method name ([5d870659](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5d870659811f92982ce95146429cf5a7ae1c3d30)) , Closes: [#174](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/174)
    - Update method name ([fc4153ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fc4153caca79cbc1d2db1965743c61cfeefb2eba)) , Closes: [#173](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/173)
    - Update method name ([b4d6265d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b4d6265d59d117f54dbc63a763aea9d0992652da)) , Closes: [#172](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/172)
    - Update method name ([d6e245fa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d6e245fa6308c3dde5ed48d28394bd5a849e7785)) , Closes: [#171](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/171)
    - Update method name ([0f9542b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f9542b10909fc1811546580329288b2bd430f84)) , Closes: [#170](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/170)
    - Update method name ([2902b466](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2902b466c13b61bf76bed2dc6134799d3eec2fd8)) , Closes: [#169](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/169)
    - Update method name ([17dc3413](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/17dc3413fb59678100fe57dc6bb4da307a26a4eb)) , Closes: [#168](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/168)
    - Update method name ([13fa5c4c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/13fa5c4cc0c5a68acd7f20724f935a432dcbeccc)) , Closes: [#167](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/167)
    - Update method name ([8054a287](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8054a287923fba30dea27d42bf752e628ff28ffc)) , Closes: [#166](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/166)
    - Update method name ([ab5c9dab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab5c9dabca0de5ab2d9f68eab61bf7453dc28435)) , Closes: [#165](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/165)
    - Update method name ([eb0a3236](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb0a3236534f16b63a5aa4b2b26710f9558981a1)) , Closes: [#164](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/164)
    - Update method name ([ae2765cc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae2765cca2d5c2d3d2648f924eadafd8bddd2cc3)) , Closes: [#163](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/163)
    - Update method name ([96c31184](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/96c3118431d993e0ae7e7477a76785db894f27c1)) , Closes: [#162](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/162)
    - Update method name ([b1113785](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b1113785c111078e0100481cb4d76e49c1a36b00)) , Closes: [#161](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/161)
    - Update class name ([b1852829](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b1852829fa4a0b24012526798dddfbfd261be667)) , Closes: [#160](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/160)
    - Update method name ([19218949](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/19218949dd56c33a6d1e8e06a8996c9d3e26b950)) , Closes: [#148](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/148)
    - Update method name ([0d3d2fa8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0d3d2fa85831f79b61b121b4a05af5ff7c857f70)) , Closes: [#129](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/129)
    - Update method name ([da8e405d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/da8e405df363cc383bb3ab4fa2bfed6a2f7e84b6)) , Closes: [#139](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/139)
    - Update method name ([9dd6b1ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9dd6b1eaf6163ab6f913ff7ec9b88c18a83d4557)) , Closes: [#140](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/140)
    - Update method name ([4036ccca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4036ccca41abb43b2d5a25b4691a1a0712b76714)) , Closes: [#141](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/141)
    - Update method name ([07a6bb30](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/07a6bb306cbb96a97f9d4f740d1f12be51ad43e6)) , Closes: [#142](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/142)
    - Update method name ([64a0f872](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/64a0f872306d695567e2136bd5ae33491c99ab39)) , Closes: [#143](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/143)
    - Update method name ([c03651b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c03651b13fdf4585a055a29febf71040be21ab85)) , Closes: [#144](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/144)
    - Update method name ([0dc66a78](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0dc66a783978d4f8da4ff9cce40735fcfe39017f)) , Closes: [#145](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/145)
    - Update method name ([9a107a32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9a107a3298b41581ddaddefcf9718f5736f1cea9)) , Closes: [#146](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/146)
    - Update method name ([78d7fd40](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/78d7fd408037ad5d1f78c49d71bad08aa9bd5028)) , Closes: [#137](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/137)
    - Update method name ([86398c90](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/86398c9040fec640d19e7431738daa4dd17def87)) , Closes: [#138](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/138)
    - Update method name ([5b3f2978](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5b3f29789ec595d659715e40023e54cc9d82480d)) , Closes: [#133](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/133)
    - Update method name ([68ab192c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/68ab192cd39ad845a2fa7a1751af1c3c58cf585a)) , Closes: [#135](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/135)
    - Update method name ([669f4701](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/669f47014b494ad01b7694c86cbc4a4421f39acb)) , Closes: [#136](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/136)
    - Update method name ([ce6b1c5d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ce6b1c5d079aeb50c9a056e23d2dc9ff6b701b2f)) , Closes: [#132](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/132)
    - Update method name ([a47f23c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a47f23c7bdeb89655854971f5781cd74db13c284)) , Closes: [#131](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/131)
    - Update method name ([77023e45](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/77023e454441a0a7ea38fd51351ef2131cdb59a9)) , Closes: [#130](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/130)
    - Update method name ([4cb25206](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4cb25206a5922ff198fc412953ada467a3aabee9)) , Closes: [#134](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/134)
    - Update class name ([1f0a308e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f0a308e66817c860b707b761918496ee5aee5c2)) , Closes: [#125](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/125)
    - Update class name ([0a77a0a3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0a77a0a3ca95301fb0ec6268bebedabbfc17ddd0)) 
    - Update class name ([b801fccb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b801fccbe6728f65c4b014bd9b5932f0c4377e5e)) , Closes: [#122](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/122)
    - Update class name ([2e3d3522](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2e3d3522ccdd362648cd104f69ccf970773635a6)) , Closes: [#123](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/123)
    - Update class name ([8a4bf63f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8a4bf63f46d8b5621f4551434a7d483b10019517)) , Closes: [#124](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/124)
    - Update class name ([d7a37ab8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d7a37ab8fc31bdef0ee6f462de5c026a1d9d1d39)) , Closes: [#120](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/120)
    - Update class name ([daef896b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/daef896ba2f7fedf0070af56c6e892e716c12165)) , Closes: [#121](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/121)
    - Update class name ([0ff031e8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ff031e886d6ddf26053da112643f8786ed1238d)) , Closes: [#121](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/121)
    - Update function name ([b90abcf1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b90abcf1b5b714e4623d9669988ace056ff0eca2)) , Closes: [#147](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/147)
    - Updated to run new update function names ([daec9117](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/daec911733663fd54320fcda5c8df240b6a536db)) 
    - Update function name ([776311b8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/776311b8ccc00da788ae57888184a7eebe28f96c)) , Closes: [#152](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/152)
    - Update function name ([655ae1a0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/655ae1a04e5c75d3c7f5b6c7713aa4a55e4e74e1)) , Closes: [#151](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/151)
    - Update function name ([24ff160e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/24ff160e6947d5a04d5bf2bdd2421d4548852454)) , Closes: [#150](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/150)
    - Update function name ([71352a97](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/71352a9720e7e0da5a1ddfaed54eb99ac63bcd64)) , Closes: [#159](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/159)
    - Update function name ([102cba65](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/102cba65d587600e05181049690a133d2e56db03)) , Closes: [#158](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/158)
    - Update function name ([c4063305](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c4063305e349e134f817761716374dab6b79bf48)) , Closes: [#157](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/157)
    - Update function name ([8d4036b2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8d4036b272114c95d6f8a5b75c384bba54c84b32)) , Closes: [#156](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/156)
    - Update function name ([3d7345fd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d7345fd6fd8a7c2433f5dcb0fb79013555b7636)) , Closes: [#155](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/155)
    - Update function name ([9e010698](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9e01069881c6b0b0b649deea35c8fd26c16a8439)) , Closes: [#154](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/154)
    - Update function name ([3bcc361b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3bcc361b49eac18e20ad121ecf325504b20be909)) , Closes: [#153](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/153)
    - Update function name ([0f682e73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f682e7345e3952e7593b554118d2a92a0c59961)) , Closes: [#149](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/149)

  - **changelog**
    - Bumped version number ([ad61a78c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ad61a78c95f924da4b519ec92373c38df3955003)) 

  - **ci**
    - Generalised script ([ab594cb5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab594cb592c7d278da240a498e02c16384908765)) 
    - Added wiki change log generation ([542e1894](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/542e18947162740bf1a53dd027081b5dd52f7957)) 
    - Ignore classes in binaries ([c173f834](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c173f834f2c76fa6e9c7a6948f83e446a92cb25c)) 
    - Updated all files to support composer libraries ([2d8a718b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2d8a718bdaae2c55591f009dcb5a2d9cfac021cb)) , Closes: [#58](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/58)
    - Added sentry, pwiki and couchdb to composer file ([00469b77](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/00469b774372a653d9f78a997592d8cb3384008f)) , Closes: [#58](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/58)
    - Added Medoo to composer file ([70cd764a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/70cd764a1288ed3a790f04ee029feff0bc6b3885)) , Closes: [#58](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/58)

  - **clacks**
    - GNU Terry Pratchett ([07f6c6ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/07f6c6eabccc57fe657fe6239195b7ca2f24f97c)) , Closes: [#197](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/197)

  - **code**
    - Corrected code analysis errors ([ee9f9fc8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ee9f9fc84c6dc0c1179507960302a2d3d592092d)) 

  - **config**
    - Config path ([5fb1dada](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5fb1dada427923d47f534b1ee60c39ed729e7d9b)) 
    - Moved config files to separate directory ([10201ea7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/10201ea7ed973f109fad356e2bd213defcdc80f5)) 

  - **console_logging**
    - Prevent cron runs writing to console ([d66d2bdb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d66d2bdb2b0478dc6a8ca8963188a813f72451e3)) , Closes: [#70](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/70)

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

  - **deprecated**
    - Removed deprecated & unused methods ([a22efa1f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a22efa1f92bbbed897cb9d0ec75ae2c835a56b1e)) 

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

  - **dot_files**
    - Updated name/version ([43ff51a6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/43ff51a64f8d165f26ede635c841dc32c9a56633)) , Closes: [#105](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/105)

  - **enhance**
    - Added indentaiton mark to NXR ([ee794236](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ee7942368992f8b34a4316339a580bd06b3f60e8)) , Closes: [#51](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/51)

  - **error reporting**
    - Fixed error getting API limit reached value ([56f9997a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/56f9997a4aed81df1dddd70fe03c0d02a51ed3f2)) 

  - **error_prevention**
    - Invalid CouchDB authentication ([629a987b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/629a987bc17ecd58ef4af7766720aab350913279)) , Closes: [#70](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/70)

  - **error_reporting**
    - Fixed wrong values being reported ([9f126e28](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9f126e2884d79a66c43145a728bcdce0ff6f9a91)) , Closes: [#190](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/190)
    - Upgrade error reporting ([4e1f5be5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4e1f5be56c6de53db0536dde910f524e635a3050)) , Closes: [#68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/68)
    - Added user option to sentry logging ([b9e2a916](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b9e2a9168d46b9dd3e3b4767f0a075de6d738294)) , Closes: [#66](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/66)

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
    - Removed user city ([c81d66bb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c81d66bb36f37b1c8c7a3a295ff2593b1bbdd211)) , Closes: [#107](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/107)

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

  - **includes**
    - Include global nxr function, rather than redefining it ([3561614a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3561614a6bcea63421549af25dbf1bdd088cc3d3)) 

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

  - **json**
    - Fixed streak errors ([9dd91221](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9dd91221df96fc19d78edf62eed13d8644958ba3)) , Closes: [#191](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/191)

  - **mobile_ux**
    - Removed graph from mobile devices ([845e4715](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/845e47152ff3a76e7ff27c0d1cd656811fdef69d)) , Closes: [#96](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/96)
    - Removed map from mobile devices ([de6bfa9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/de6bfa9bab75aa550987f8362b554301c546d58d)) , Closes: [#97](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/97)
    - Updated dashboard grid display for mobile devices ([d3442f04](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d3442f04c3c5a22c74d4e5aab780b72f06e96afb)) , Closes: [#99](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/99)
    - Updated dashboard grid display for mobile devices ([dc62b410](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dc62b4100c1286f0a13e1ad0aa814015b35a431f)) 

  - **mysql**
    - v0.0.0.8 rebuilt badges tables ([759121f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/759121f91a84cc3efa2479666b78e4682ccea4d4)) , Closes: [#65](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/65)

  - **namespace**
    - Updated test namespace ([72585aa2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/72585aa257afdfce35b95ec9e234e86b6a773f1c)) 
    - Imported core namespace ([8a92ef16](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8a92ef16e1e51173c9fe11b3cc8f8345ab925866)) 
    - Created MAP namespace ([44030f5c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/44030f5c6b0dfae1310cdf8744324c20e59e347d)) , Closes: [#126](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/126)
    - Created UX namespace ([c9ff7108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9ff71087710801f34c359cbb77c69c98d6ebb7a)) , Closes: [#126](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/126)
    - Created core namespace ([6e7e4b3b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6e7e4b3bb4cd5ff4cb1d35d352c272012a31abe4)) , Closes: [#126](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/126)

  - **nomie**
    - Dont query nomie if user isnt setup for it ([d2a647aa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d2a647aab6e0495c1fd3d9886bbda3acb9fd0695)) , Closes: [#229](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/229)
    - Fix for some misplaced array elmliments ([bb5f5ee7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bb5f5ee7eb50450929ed67b3cf863f9979a25b00)) 

  - **nomie pull**
    - Added progress bar ([872a5dd7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/872a5dd751eb671bd7417e04c36aea1cb3315ec7)) 

  - **nomie upgrade**
    - Updated Medoo syntax ([ccae87d0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ccae87d0836085eb0846cbf736751f93df38b9a5)) , Closes: [#187](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/187)

  - **nxr**
    - Removed additional space ([bf59bd02](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf59bd02790fb801808cb58e62daad2e18a7ecb7)) 
    - Fixed indentation ([f1705872](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1705872191205e16d476c466d4db0a67351e0e1)) 

  - **php**
    - Crushed some more divison by zero errors ([701a3444](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/701a3444ab94a7a91318537b71b30110161e1d84)) 

  - **phpcs**
    - Changed exit rules ([58f3260f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/58f3260f01518d0c5d877207d8a619e93ff9b358)) 
    - Each class must be in a namespace of at least one level (a top-level vendor name) ([dd814b8e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dd814b8e2e0c17b8b8cac78a088206ded6549504)) 
    - Visibility must be declared on method "haversineGreatCircleDistance" ([2f18eabd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2f18eabdd47fa43dff5ea3cb201958bb610ea668)) 
    - There must not be more than one property declared per statement ([9a222730](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9a222730dcff06a6553585c14b694f5bcc923d35)) 

  - **phpdoc**
    - Cleaned up PHPDoc Markers and Deprecated ([2f222748](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2f2227484a3218260c741ca966ff000f77efcb04)) 

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

  - **security**
    - Sentry DSN Key ([02fc8d40](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/02fc8d40007fdd0898dec2f82dd1db8f34ab9f6a)) , Closes: [#74](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/74)

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

  - **upgrader**
    - Removed SQL Foreign key checks ([7f8bb58e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7f8bb58e7b2575f36abbca811d898d64f86fa8da)) , Closes: [#69](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/69)

  - **upstream_update**
    - Update djchen/oauth2-fitbit from 0.2.0 to 1.0.0 ([bec7a26c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bec7a26c45139ba4f7756d339262f024b2f5393f)) , Closes: [#184](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/184)

  - **user_ux**
    - Fixed grid ([3681e815](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3681e8152dcc8ee6694eb77deea8f1efb9be0b0a)) , Closes: [#106](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/106)

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
    - Fixed weather icon ([58bf36f4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/58bf36f4187e9722ad5b66cd043be9d1ca5f2818)) , Closes: [#189](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/189)
    - Fixed class inclusion ([0ccd79a1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ccd79a129498df7d06a541ef8739b15b0aba059)) , Closes: [#186](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/186)

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





## WIP
  - Added error alert when an ID is seen grater than the database size of 30 ([344d38d6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/344d38d64eeb87a4efac45e58f1dbfa64b932800)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added database error check to all DB queries ([8558667a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8558667a3677c1a33ea7d617d819d4fc5a672b4e)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added wrapper to check for database errors after each query ([a9168ea9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a9168ea9b7f5e25d506cedb44e14f71f13698950)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added user context ([917367d9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/917367d916541fed452db7c38ebc2b7847456aee)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added log levels to report messages ([b075dfd3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b075dfd35d0959f2d2f822bd3e3871a687936185)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Updated sentry environment values ([c037f23c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c037f23cb4bebe51ec21712f3995fe6ecbe03370)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added Sentry API library ([6522dacb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6522dacbc2742968267f19c372d61f2a5560e8a9)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added error catches ([827cfff3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/827cfff356cb5288db3bb2f9f5a23b46e676b4cb)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Removed function errors Added NXR output to confirm error reporting ([43dddfed](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/43dddfed3b7c94b064999cc8c21aa860ca1ecf80)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added error reporting to command line wrapper ([b709987e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b709987e24268d17cb6c94ad58418235b6dcd3c9)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Updated error class to mirror sentry class - would make it simpler in future to replace sentry if required ([1a9ed6f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1a9ed6f3c771b40da3a127fae0601265a46be7b1)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Added class to handel error reports, built into NxFitbit class ([7d53a826](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7d53a826da05754e6d54a38131f520b9e03bcab9)), Issue: [#50](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/50)
  - Limited returned rewards to 15 ([c5bb0905](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c5bb09057a65a9160f2f4976a4870ef11b933f3e)), Issue: [#49](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/49)
  - Added percentages for distance off breaking previous streaks ([3a5e3df9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3a5e3df9f5a0740391660a0dd2bca7a5455f7c97)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Initial breaks to check for age since last good record ([42688870](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/426888706e225c1ade2e0f197fbbddd48a2cc435)), Issue: [#44](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/44)
  - Implemented streak check at every step call ([fffd286d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fffd286df55cfed5fbb5d95f2ea8fb7d31e3e0bc)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Only return steps streaks ([95e49a6d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/95e49a6de2309e645d9e04e9bf7e9492125ebcf7)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Updated table definition ([e8243271](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e82432719c0ab37c90f06bc1121756a44ac503a7)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Added return features ([fa89dc28](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fa89dc28641a1c4949247dbc8e68f7118f5750fe)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Added update function to support new database layout requirments ([34465183](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/344651833439e5415ea11068ff0895f084bb4471)), Issue: [#43](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/43)
  - Started by adding user settings value for Nomie DB keys ([c8861ee6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c8861ee6468ee847dc1800af5e5934d36220166b)), Issue: [#42](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/42)
  - Added cool down and error support ([89a8402e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/89a8402eea1455ddec52d909461c3b5d13d761ed)), Issue: [#39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/39)
  - Added some progress output logs ([d6f3cc82](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d6f3cc82bb0d4c281ee365a584872f45cea4dd7e)), Issue: [#39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/39)
  - Cleaned up the icon field ([c79153af](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c79153af7ffa4b6347c08bfe9a820b9583d4d5e5)), Issue: [#39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/39)
  - Added Nomie Tracker to supported actions ([94e128d0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/94e128d038e1ec3199b9339e230703b0f5b595fe)), Issue: [#39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/39)
  - Added initial datareturn for Nomie CouchDB and included support files ([4201a1f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4201a1f1bafeb7d7485f8b16306bd34caebf534b)), Issue: [#39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/39)
  - Added seven day analysis & beat yesterday ([88fe5519](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/88fe55196652e687c986124bb36e7dae4268dd4e)), Issue: [#36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/36)
  - Added number_format human values ([9377a538](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9377a538b7db13702bc5743d1a073cbb109ce0f8)), Issue: [#36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/36)
  - Added week step goal ([28de14ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/28de14ee3db3479dd04ad6597accd9fd3cc00a01)), Issue: [#36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/36)




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

  - **markdown**
    - Corrected indentation ([dec1448e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dec1448ee317e4ecbc568acbe992ef7ad074847b)) 

  - **new_db**
    - Created new XP DB ([a8411f3f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a8411f3fd4b5f6cd384bad7ba35c33e4354e2ffe)) 

  - **nomie**
    - Show trackers with zero events ([9267fa78](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9267fa78fe197654be7488f9a40191f2ac7499c9)) 
    - Bumped version number to include DB changes ([2377dbbe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2377dbbe9b3294dc7f5879e68b9a9751d8867548)) 
    - Expanded map display ([52fc5a99](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/52fc5a992aaf4f5efd35c8a30d0b0b3e18648e91)) 
    - Moved map to model ([c0a8eb68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c0a8eb683538fc724dc5170786961bdc2cffdfa0)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Don't return events without GPS and limit number of returns ([ed8a93b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed8a93b4129c645b7bc8d3af7c015f3b590228a4)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Added map of events ([e6581454](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6581454f7e87b186e967d963495da461eba0aac)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Added GPS to api ([74e79474](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/74e794746ad1752382e33eb39ee9dd3ee39eebd2)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Added tracker value to cache hash ([9d9d3227](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9d9d322757c5773d94ed97b6aecefff33c64ea7c)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Created new Positive/Negative graph on the dashboard ([c49f2b56](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c49f2b56f698e49b07bfde05033f10c4073658f2)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Updated breadcrumbs ([d0e10e27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d0e10e27bd751f28cc8f49625522f3325c002f81)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Updated icons ([ff6a0aa8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff6a0aa8c7bf36e13d521f215242894f42fb6b47)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
    - Expanding Nomie ([5e6d060a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5e6d060a0a2119428ddef03edb50bd1a35e2f47b)) , Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)

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

  - **settings_privacy**
    - Added static image maps and initial support for json privacy datareturn ([7c760b3b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7c760b3b76ee4f1830f32907e1d45490845d05c8)) , Closes: [#110](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/110)
    - Fixed address search ([e100af6c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e100af6cf577cadecd1d2ee25fccab50f39049f3)) , Closes: [#110](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/110)
    - Added inital address search ([48f74652](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/48f74652f97b3e6c394dc39ef8bd878a58c3c4f4)) , Closes: [#110](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/110)

  - **sidebar**
    - Updated sidebar layout ([f9884fd8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f9884fd82624224046a4c5ab9620c70847e8e1ca)) 

  - **user_ux**
    - Finalised shipping UX ([d9993af5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d9993af51102de6d4ae00b16f4ed38faf88dd866)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Completed test ([a4a5cb9c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a4a5cb9c6c6fb2b6d77cf1dbd5e2b0c6d2c3a8ea)) , Closes: [#95](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/95)
    - Inital test of register script appear okay ([ac522274](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac52227488c2f67ac4316bc613cf63b425f2814e)) , Closes: [#95](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/95)
    - Tweeked gride style ([ab8fcff6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab8fcff6637223ab1e460a890a764db3decaf1e7)) , Closes: [#103](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/103)
    - Tweeked gride style ([f3c24f69](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3c24f69b99a91cc808a46aee481a7f920537c16)) , Closes: [#101](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/101)
    - Tweeked style ([7154269d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7154269d9c5dfc918362a781d61e4713a2cded0e)) , Closes: [#100](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/100)
    - Fixed mobile grid ([5f3b19a4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5f3b19a4c4188cf4ab50d41b22abd8bbed55867c)) , Closes: [#102](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/102)
    - Tweeked date style ([dd5ed79e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dd5ed79e6b36775b3942f81c4e1395eca574eb88)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Updated link href for JS onclick action ([eb6208b3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb6208b33368c3e13618fdfacc077fb608115742)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Fixed mobile grid ([a111c474](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a111c474f7ac4763e1ba78c88cc157eb53d310c2)) , Closes: [#102](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/102)
    - Used correct libraries and updated style ([ac219da0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac219da09ac34c2621dd31d9f85614da62844a50)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Updated html headers & included config ([fa21b905](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fa21b905b2001e2a207ec75fd18143b574c1ab77)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added refresh menu item ([326843f4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/326843f4d2f41e5fb51b7943dd0ff417125f57c1)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Corrected CoreUI page intercepts ([d905be9a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d905be9a8f19f16a5021248b19704bf52b2ad760)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Started work on authentication ([b5811bf5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b5811bf543f5ae441c89e6daf7653ebe65233a37)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Cleaned up Activity Log ([3e44302e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3e44302e48e76b4157166875bff7fa093917caff)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Activity Log ([5efae896](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5efae8961087ed74273ee71dc0867aa349a222cd)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Corrected timeline cards ([a316fad6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a316fad6de7ec5eef42adda0f872ed53305ae131)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Corrected map ([4f223a92](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4f223a9213dde226e962767287a881ef7784db41)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Started Activity Log ([ec598816](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ec598816bbb46c84b01c6d5c2592fd2d1977fe3f)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Food ([e73f546b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e73f546be873464c72f14bcd591b2e90402b17a1)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Body Fat ([a63991de](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a63991debc45187813e602ca14d933668649c22a)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Body Weight ([061f97aa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/061f97aa1c75fe81863add69e250ced9215f1010)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Commented out new features ([ff3d7cd9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff3d7cd9244eaa6b733679952a1dfb57ba634f0a)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Removed fixed date ([389448df](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/389448df9272ad45ec2c9382ca9f54456bce01ff)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Activity ([a30787a2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a30787a2c4239f862cabfd255db868fee32cee6f)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Leaderboard ([94b19201](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/94b19201912e47bcf466abb133f4e5341e55d034)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Finalised Badges ([2434aeb9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2434aeb9508d874fcdbf8b92a1a258b3dc8cda4b)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Finalised Devices ([679796ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/679796ee880d7a02d0aa5e1da3aa7963375b65e0)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Removed WIP indicator from Nomie ([a9e466d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a9e466d3f783363697996cd4eec1542476de1d52)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Finalised Nomie Trackers ([29f05890](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/29f058903fd282c22704f27a7d266e2e082ea753)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Reduced duplicate imports ([0000a096](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0000a096b882e8e9ac642ab0a13b6987261fd5fe)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Blocked out WIP ([aa70a07b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aa70a07bf8061b3861eaad8a8452b1fe404605e3)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Updated sidebar icons ([f1bddbd2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1bddbd23d4f9b1c792c797fa054f6970363818d)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Badges & Leaderboard) ([bee3e721](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bee3e721095eb1c422cf047f4080a0e9bceb8e8c)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Sync Progress) ([f007a104](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f007a10461f8322bb44100d35b05890b4d4dff3f)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Feed) ([2aaa01ad](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2aaa01ad4297047e0256bf1cfbeedba6e8cc8a2b)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Devices) ([e19f7df6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e19f7df641dee4a6ee9da21f36a3d7e64fd19346)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Nomie) ([ea4cc35d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ea4cc35d8e28449be46fa12152e2a537833b2b04)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages (Food) ([337499f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/337499f15e522d6315c78feacf79c15d88e1fd33)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages ([f3787e70](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3787e701c3f3949041c719aaeb843559aca68f2)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added debug gen time ([65d151c8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/65d151c856da815f52e792410aec0eca57c655bc)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Created dynamic breadcrumb ([f867562b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f867562bb75629a3b370d5a663f89d73db7b6abe)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Setup JS/PHP environment ([1f1d286c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f1d286cf12b48fd372f423485ce324f08ab5618)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Updated paths ([b0671ec7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b0671ec7b427996d81969e0258c729086cfa3ffc)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Setup session ([c5dfcae0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c5dfcae0a5570b1c8fc11e6843437a1199906627)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Basic user items to user menu ([9b8bed27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b8bed279b8dee7bccfcddfe4dc739d28b39b54c)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added new boiler plate pages ([688d1b12](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/688d1b12544c68f4be747172c35d72bd164b4000)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Corrected inclusion indents ([47578854](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/475788549cb129dfea14e3c15682813a98d75bf7)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Removed border round panels & icons from snapshot ([0dda90d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0dda90d4ca05fa4bf2006bb9a4a90dab52487819)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Local images ([12c70c24](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/12c70c24e7985b21b38e873bdc51d7b78a8bbd1d)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added helper classes ([7d446788](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7d446788c3c30046312604491b1db2962af2683e)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added user ID to DOM ([003b4859](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/003b48593ebb1a3d17a6f5f1d6419e24b75417eb)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added basic dashboard ([6a1f57e5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6a1f57e50482e8966494baacbc83b0f5b6277822)) , Closes: [#75](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/75)
    - Added global JS functions ([a66dcce1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a66dcce1bcf58d0d015b64fcb2c9f4ba820eeb4f)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73),[#92](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/92)
    - Added Weather Block CSS ([18722e28](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/18722e28b1dd968c1aea632779310604fe3428c9)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added Simple Weather ([41fb335e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/41fb335e29be04ce30b9c0332be15c28f52d3a5c)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Setup HTML include file elements ([c000d5ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c000d5ac540176916ed94a2245d52f3031aa2633)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Update logo sze ([60934c6b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/60934c6b511e26c485e81da2e2193fe1ae02e96f)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added Nomie icons ([06405d40](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/06405d406dd1af1a4fdd00deb8fbd7ab4dbb6ae9)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added support icons ([0c93d183](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0c93d183d0628fe0412ab129a78431118b3fe710)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
    - Added template admin panel ([41fa29eb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/41fa29eb5a0bacc642b625ea7a9850bddcb23ff7)) , Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)

  - **ux**
    - Added Journeys ([391eeeac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/391eeeaceaa019a2a2263f2534b06ac53458aaaa)) 
    - Added Pushes ([c2764f2f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c2764f2f88c27caa4c76554a8acfbd377f0f2f64)) 
    - Added push settings ([dc029838](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dc029838f41badbea0d11b0eca1a2549107fba02)) 
    - Changed display when no push active ([38a8f8db](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/38a8f8db00a1086ccc6fc7d62ddf66e8a6e2009d)) 
    - Updated display layout ([450a707a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/450a707aed9328b2bc107f8bd2b2336239ba3bc9)) 
    - Added Push support ([925b1e7c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/925b1e7c333beb14409c3316b9dd48f509ecb33c)) 
    - Added JourneysState support ([5994c6ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5994c6cafa31d81c3bc68336aae3a21c92992b68)) 
    - Added KeyPoints support ([d4eadc71](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d4eadc717bc0a91ede034796fde99ec5733bd248)) 
    - Added UX page to display sync progress ([c31c02c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c31c02c9793f686711189a706cde3816f2e3fb54)) , Closes: [#90](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/90)

  - **weight_in**
    - Updated JS to support new return data ([e7b6cddf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e7b6cddf2e8f067968a0970af43dffb2b998bc92)) , Closes: [#109](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/109)
    - Return weight in values ([9479552c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9479552c3ea306002761a224948b79b0daf8c3ec)) , Closes: [#109](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/109)





## Documentation
  - **change_log**
    - Updated template ([9ccd0ba6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9ccd0ba6e089199c5b3bd6e1e823d29ddb6757cb)) 

  - **changelog**
    - Updated changelog ([ff2dbf55](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff2dbf55394787925f66047bb5147f0cdb3f4d2a)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)
    - Updated changelog templates ([b31af2b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b31af2b47616983928064a12b20fd0cf236eba18)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)
    - Added templates for changelog ([e78d60e4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e78d60e4a5dae63183b782bedfb391805f166065)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)

  - **commit_template**
    - Added commit templates ([6bc80a19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6bc80a19559466f0f3fccc502a515d808b2c6123)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)

  - **markdown**
    - Updated License ([88b22764](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/88b227645106746e60431f707ef9c00402a6291b)) 

  - **phpblock**
    - Updated block comment ([24228328](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/242283288dfe803a1ba178fb173652f45065e321)) 
    - Updated param ([675bcedb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/675bcedbc808d8e68aa4fdf9b5892d3b0e600ccd)) 
    - Correctly defined @param variables ([bccee77b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bccee77badd96331438f90716c90001dd09a8600)) 
    - Correctly defined @param variables ([81b70281](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81b702817a670831c6340d645f9aa7f3cade2abf)) 
    - Correctly defined @param variables ([67b7e929](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/67b7e929430e58fe2e5ce79e76cc1e4c1ae30b15)) 
    - Correctly defined @param variables ([2347bea2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2347bea26a50da0a1bfac6d106e35d5afff87e57)) 
    - Correctly defined @param variables ([5c585538](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5c585538d8236508d7265b17979fd9033fa3ef0c)) 
    - Correctly defined @param variables ([2a30910c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a30910cb8dc784f11c111938236ccc1b228dab5)) 

  - **phpdoc**
    - Updated method docs ([c34f629e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c34f629e352e39d1b5d92029bd96cd95e256d7d3)) 
    - Completed config class ([4a5e40c2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4a5e40c2c08c40aff5fd84a7790b344f492bb3d3)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)
    - Added class comment blocks ([b208e3f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b208e3f2ae1b0fd83b86cba2649ba905d57dd1b1)) , Closes: [#63](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/63)

  - **readme**
    - Update Project README ([fd93eea7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fd93eea7113b5f8c707e1a7011754ff4e4fdceb4)) , Closes: [#67](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/67)





## Refactor
  - **$_session['admin_config']**
    - Rearranged if logic ([6d1babe2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6d1babe2fe10a1d470d186718d9b02c6b1d56e2a)) 

  - **change_log**
    - Changed text for master..develop version name & added wiki links to intro ([db6dbdee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/db6dbdee9a5631b878e65c44b4161bec6a511ff0)) 

  - **ci**
    - Only run test for develop branches ([8b2eeb1f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8b2eeb1fb910c8114704525258b987a1e915cc80)) 
    - Disabled CI ([37dcda04](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/37dcda04969d2420c708c875019304b52bec0f0b)) 
    - Allow all tests to fail ([8b45be4d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8b45be4ddd73cb5578d697fe48dd3a26b5792078)) 
    - Deployed to staging and production server automatically ([33b276f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/33b276f2428d978ae06bfc34cb2e6025ab10d3e4)) 

  - **code_review**
    - Cleaned up code as per Intellij IDEA ([9807f240](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9807f2402fd7f0f9288ce7332dde42e74aed7218)) 

  - **cron**
    - Added nxr output after completing CRON job ([6f5ecc55](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6f5ecc551721f30640a1920a0f3df80de1738e9d)) 

  - **database**
    - Removed duplicate database connection ([505c10f6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/505c10f6e107f2d1fd789b0dbbb19f5b7f030eda)) 

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
    - Added table update for new sleep data ([844387da](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/844387dad967790b861a1d453ef069e5fff93730)) 
    - Activated heart rate data ([c7aeac73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c7aeac7313161b03a959e8bd50e6571d5153ab18)) 
    - Added support for new sleep api ([b1ebd5a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b1ebd5a7b146f548aaaa4d0713266ab42bbbaf16)) 
    - Added blaze, alta hr & alta device support ([83c667fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/83c667fcf13ff5ae5d709eb2b772a7ddc2b97b58)) 

  - **gitignore**
    - Rearranged ignores ([609d1405](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/609d14054f21cd63f5b43a1151d8e92551fd7e08)) 
    - Added ignore folders ([9bf8a737](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9bf8a737f4ee5e9fba532d922d79384bf58ea622)) 

  - **habitica**
    - Updated criteria for healthy meals ([170a9f89](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/170a9f8967fd749f9f04f095ad3739b9ab4e2e24)) 
    - Looking for users habitica creditials in DB ([3d82e44a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d82e44a3e1d02ec0c44dd851044d9dba3ed2fc3)) 
    - Used ENVIRONMENT definition to decide if which habitica server to connect too ([40049027](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/40049027605927f5bf35b9a4609098dbdbaffe8e)) 
    - Used ENVIRONMENT definition to decide if which habitica server to connect too ([e6896f84](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6896f84d264645b3e49ed3a4f38f2b7ae91e7c5)) , Closes: [#263](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/263)
    - Looking for users habitica creditials in DB ([cd648a51](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd648a51a5c79d5f27e5393a0c8ec83940d9bce1)) , Closes: [#262](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/262)
    - Changed config key's ([088a443c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/088a443cc9b05f827d6fcf9ddb1e732ccc3ffa35)) , Closes: [#261](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/261)

  - **ignore**
    - Updated ignore file ([cb8c5bf3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cb8c5bf3f84f2be7a30b32e70773708a4e6f7a3f)) 

  - **json**
    - When debug active return JSON array as plain/text no application/json ([582ee9f0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/582ee9f0db997685e76b4d9d72263f8ced698900)) 

  - **login**
    - Added remember me option to login page ([51a2831f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51a2831fc21f555fa22b21d29f6d9e6c983a33af)) 

  - **merge**
    - Bumped version number ([a4f4da1e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a4f4da1e6e1c18eaddfcf7e971681238d2ad6f9a)) 
    - Corrected line ending ([421556c2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/421556c26980a7e6009c1b03551e063869203526)) 

  - **namespace**
    - Namespace reorder ([7609f2fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7609f2fcd5ad8813c1bbfd40a44eaeae7fe858e3)) 

  - **nomie**
    - Added additional information to Nomie tracker map ([e5486251](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e54862510e0bef6c528eee4005f357871111677f)) 
    - Changed graph data order ([b3caf266](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b3caf266dde7fb38fbc5db41cbb10a977c23d676)) 

  - **nxr_destroy_session**
    - Added nxr_destroy_session ([bfb08779](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bfb08779783a8c0155366aa6846a4cd8901c8d91)) 

  - **phpcs**
    - Updated code style ([e3e07c5d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e3e07c5d39af6acf583a0f4bd38656db0d1acb65)) 

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

  - **segregation**
    - Moved NXR function ([d84c5ae1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d84c5ae101f2eb0405141e5302e2ed3f8c627c75)) 

  - **settings**
    - Moved active intents settings into seperate settings page ([50dfbfc1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/50dfbfc1a6e37e76d12483c54404fe06836ec26b)) 

  - **style**
    - Updated code style ([ff0128b5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff0128b512492a71a128106795a42bbbbbb6dd41)) 

  - **update**
    - ignore vender file ([97acf957](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/97acf95738735b92aa3c4387002290213a199f56)) 
    - moved docgen to staging ([0bf7949a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0bf7949abdc82b588ff8e69ad101f4073a0fb252)) 
    - Earmarked database linkage for 2.0.0.0 ([a04b6519](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a04b65194c347e66ea7ee3d69be0dc7feeb81499)) 

  - **upgrade**
    - Updated version number when no update functions required ([98b2b241](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/98b2b241663c8b1ef3e17c90dfbb8a6468865dd5)) 

  - **ux**
    - Updated icons in sidebar ([63ce32fe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/63ce32fe8eb20713f8368a191091b3cccfe797ba)) 
    - Added blanker space for future journey support ([4bad15f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4bad15f14aac8f32227cda262ba88f8f9a2ced42)) , Closes: [#246](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/246)





## Style
  - **code analysis**
    - Correct analysis errors in CSS files ([50ccfb42](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/50ccfb42e57a56fcf22d6c99bbf01cd3b142e98a)) 
    - Correct analysis errors in PHP files ([f3ad8785](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3ad8785c4f853dab5abc6a1e7659c4f781ba217)) 
    - Correct analysis errors in JS files ([8e0865f0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8e0865f0adf71e22781885569c0aeb534cca66dd)) 
    - Generated missing phpdoc block ([ab1ccffe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab1ccffec8726037cd35bea5510719742ffd321d)) 
    - Suppressed warning about dynamic class inclusion ([c88beff2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c88beff2f17f52219e0ef9010bb3244604795451)) 
    - Loading classes into test script from autoloader ([0f1392b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f1392b4355ee841402ca13b9f449b2c1d49281b)) 
    - Added support in autoloader error for undeclared ext ([789e1b2d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/789e1b2d3048f1e7ba807cc2d7a40f400427f5d3)) 





## Test
  - **config**
    - Added test for config::getRelatedCacheNames ([bae52fff](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bae52fffce8f71a62fb938d2edde0dfaca0a3d2d)) 

  - **phpcs**
    - Tested exit state ([ea1a3cf3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ea1a3cf3750c3ddf9cc6257593f2d8bc1d61f2ac)) 

  - **phpunit**
    - Updated exclude dirs ([e2360f6b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e2360f6b3cdd41466561ab582e49a1b9a91a9dd8)) 
    - Updated composer path ([c84cfaa0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c84cfaa00cb36062322e9983b0a87d01aff330cf)) 
    - Added phpunit.xml file ([dd160538](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dd1605386d92640847aad3a650327efaa5a785b1)) 
    - Updated unit test ([c9428837](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9428837b7144cc6ac9512b90f6186e38635dfa5)) 
    - Updated dev env ([c2daad97](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c2daad97374cdde96c4e53f9ad53988a3f65874e)) 
    - Updated unit test ([48c4b7b2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/48c4b7b2266d725bdd37118f657c8b4d6cef256e)) 
    - Updated array value check logic ([88177fce](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/88177fce04706fbeff385b124fdd3b71afb1a598)) 

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

  - **case**
    - Update case for class file ([beb7cc1b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/beb7cc1b98378b8fb91e27f7351850bfaa4af5f8)) 

  - **changelog**
    - Updated changelog files\n\nBumped 0.0.0.8 to 0.0.0.9 ([85bbc00a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/85bbc00af431ead2af39b51b3e0934e116915d0a)) 

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
    - Updated scan dir ([c1b2d021](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c1b2d021d30c1e94ae482a12d5bea91d0b2cf14a)) 
    - Updated staging url ([79daa16b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79daa16be202868455df48a43cefda87f7d2ef44)) 
    - Updated review & staging stage ([1dbfb45a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1dbfb45a3f08eda327ca38045555c77c3c587664)) 
    - Updated review stage ([c7c1712c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c7c1712c76636db54efbc5a525f642d0efa208a8)) 
    - Updated review stage ([165d0bcc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/165d0bccb612b07cf8129c368dc734d4c49b60cf)) 
    - Updated review stage ([542442a5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/542442a5a9bfe096853843e9ff5d571570b95184)) 
    - Updated review stage ([cf67eac5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cf67eac5ff6c54249badd71542484954668b5ff6)) 
    - Updated production stage ([744ec7ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/744ec7ea5d274320cdba3b04f8b913d5146c9e6e)) 
    - Updated staging stage ([fcf6d2cc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fcf6d2ccc53c8e5f8553d522266bd23b8e5a1e30)) 
    - Updated review stage ([ea1240fa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ea1240fa8e7f8b034bc78c9803c2780711f174c4)) 
    - Updated review stage ([4dc1cbd9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4dc1cbd944abc87db8f01d5d5b3ac250b1a0a280)) 
    - Removed documentation builder ([20a0723b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/20a0723b9361492b0ff2723a051252092208297d)) 
    - Update .gitlab-ci.yml ([986e3920](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/986e39203413eb2d2381e6ba7881eb6132f95082)) 
    - Updated ssh for review deploy ([2a73a203](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a73a2036ec530878f4107e08b9fcbedd778b637)) 
    - Silence fixer ([96d4eff4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/96d4eff4b4b38fbbaefb0812ab3729fa8ee9490c)) 
    - Updated syntax check ([b4d7205e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b4d7205ec89b3fba3cb9f183cf1cf9c616ac662f)) 
    - Fixed unexecutable script ([00affd26](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/00affd26b32f448dcc42f65374dcf2225be2c251)) 
    - Added build env script ([e8cf0811](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e8cf0811221f65118ccea500f8db36c89209a9b1)) 
    - Added PHPCB Fix before running tests ([27e9056a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/27e9056a3a2418b7aa83dec991d55473729493fa)) 
    - Updated php_codesniffer ([3659e435](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3659e435b289565060483068d4e8add0548d0114)) 
    - Corrected spacing around ! ([745a5cf7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/745a5cf7aa438cffdc6dd13cdfe762b0c5038929)) 
    - Updated tests ([5f36d894](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5f36d894fc910469fb5f723d852d0815ebaf8325)) 
    - Updated PHP code standard to PSR1/PSR2 ([04b9c3f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/04b9c3f24b96d0a6c2e77a0a7080ee98f3e39dce)) 
    - Updated tests ([ad9b9e1a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ad9b9e1abdc2d4c69d7bf85e257cc317219bdfac)) 
    - Merged tests ([71c04ef6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/71c04ef6613c475fa9490a91cb76db3f7aa71663)) 
    - Updated pre build ([b21dfe4c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b21dfe4ccda8a2412b2423b6639f89bd6b1b0673)) 
    - Fixed stages ([8a6e19e6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8a6e19e603fef964a6699c20eb8310744b274178)) 
    - Added more test checks ([6ea1ed62](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6ea1ed6291b9c0d18ea7494cae150f7e78f9bbc1)) 
    - Added stages ([63927cb6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/63927cb68216142a1472b9c22ea48e31668f1989)) 
    - Updated README with build images ([01e076c8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/01e076c8b42b81b92d55fb2dd197a07362952e9b)) 
    - Updated Gitlab CI config ([b1f32081](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b1f32081e80ef898168ef72d01e473dc52d3b630)) 
    - Trigger Build ([6339d7a1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6339d7a12caa1981ac3f13f372d658acdec838e5)) 
    - Trigger Build ([1a8bd9ce](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1a8bd9ced188a5d40400e483a4a9a02ba25587e2)) 
    - Trigger Build ([d1ee5c6b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d1ee5c6b2630774c2abec5e9e67610c3a7cfe527)) 
    - Added Gitlab CI config ([93d36cd6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/93d36cd65df43830ccfd943b59226646557c8ab2)) 
    - Removed Gitlab CI config ([0375a288](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0375a288e04aad9500bbc1e38e4f82cf0294dd0d)) 
    - Added Gitlab CI config ([eed6f216](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eed6f216392b7ce01d2dd74aa2f2340b21661fe9)) 
    - Updated README with build images & created PHPCi config ([17e710ab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/17e710abdee9b1bdfa29dcbcc63766e5c26ca08c)) 
    - Added build status ([34314840](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/343148403438f3ea468ab146e8966db09141c8ea)) 

  - **commit_template**
    - Moved commit template ([dae715d1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dae715d1402f93c24863555e0b62f3e949b7558b)) 

  - **debug**
    - Removed autoload debug ([f085e5bf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f085e5bf67174edb72af9f94b944e08eeda9bc82)) 
    - Removed debug line ([9b56718d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b56718d393567d634b367cf97ef41ca4eecc3f2)) 

  - **denug**
    - Removed unrequired debug code ([7599e47e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7599e47e492f538fdb644d21f2381255d006f786)) 

  - **deps**
    - Updated Composer ([ab729d9a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ab729d9aa85335e623a498ed81945d3a2d2586e9)) 

  - **device_images**
    - Added device images for Blaze ([3644d590](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3644d59019356e5cf62fb30bef33b5d670ae8b36)) 

  - **git_ignore**
    - Ignore lock not json ([89c40bac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/89c40bac6e9fff683635f3c0f1a8037407a72bdf)) 
    - Updated ignore file ([b2c91c7c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b2c91c7c42f4e73cb2d7468cf0a0493ee5602e15)) 

  - **gitlab**
    - Added new dep template ([28f60ec5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/28f60ec5ec2651142aedd0f2bb524711efdd8d55)) 
    - Added new issue template ([7206694b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7206694be738453f11b5b2eb561b80a073e636a4)) 

  - **phpdoc**
    - Updated template location ([a21c5948](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a21c59484d424a089c27a4dad15cb3224ae7848f)) 

  - **readme**
    - Updated README ([a6ce7d68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6ce7d687288e1fb5773adf86f87a6dc55640fd4)) 

  - **rename**
    - Update dataReturn.php ([6c7eae27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6c7eae27064fb7b01ef822d81c39ff7312c508e4)) 

  - **style**
    - Updated SCSS style ([14153626](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1415362642a2c576aba4e02edbd7b61a6f461c5f)) 

  - **upgrader**
    - Updated spacing ([b47c1619](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b47c161995e7386b325036a18621d72fe7349533)) 
    - Changed ` character ([ba254fc9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ba254fc9a24414121fb49913debf84c7f6d6cccc)) 

  - **version**
    - Updated version number to 0.0.1.0 ([51e1ff3f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51e1ff3f9c37b3adf4a8522576714c07bd6b1920)) 
    - Reverted version numbers ([8dae8c52](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8dae8c520c0e5134677d570c48eefb9a77d1913c)) 

  - **version_number**
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
    - Bumped 0.0.0.10 to 0.0.0.11 ([fcbea508](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fcbea50852776c648d16afaf23f64b9742b2fe81)) 
    - Bumped 0.0.0.9 to 0.0.0.10 ([86aa9bd8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/86aa9bd89efeeb35c708c8370d9c921869879832)) 
    - Bumped 0.0.0.8 to 0.0.0.9 ([599aba60](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/599aba607315ecdb5cd23ed89333d518afdb8add)) 
    - Reset version number ([332fb71b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/332fb71bcbc64280ef7d44d8b62cbc6e4ec6ad3d)) 
    - Bumped 0.0.0.15 to 0.0.0.16 ([59e49d6a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/59e49d6af96e054b5d49990f5c6af16918ee6d61)) 
    - Bumped 0.0.0.14 to 0.0.0.15 ([f1c25e34](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f1c25e340ed36b590ddb56bdc5e8f2b82cbeca14)) 
    - Bumped 0.0.0.13 to 0.0.0.14 ([ae8d8ad4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae8d8ad49daaabc2cdc33ac99cf9dd81d8235dcf)) 
    - Bumped 0.0.0.12 to 0.0.0.13 ([d4330f42](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d4330f422701a7ddeea3d0a0ed79dff49e1b7d97)) 
    - Bumped 0.0.0.11 to 0.0.0.12 ([6345dfb1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6345dfb12d80d5d6f5d1193f17314d8194d4f3ca)) 
    - Bumped 0.0.0.10 to 0.0.0.11 ([ed5e48fb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed5e48fba0a82170d87caeb360c1fdf6cfff519d)) 
    - Bumped 0.0.0.9 to 0.0.0.10 ([30cbe606](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/30cbe60609d550e8ca7564e8d6bd2c0fa734a10f)) 





## Branchs merged
  - Merge branch 'feature/issue_230_232' into develop ([c0f874a0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c0f874a081f9bc047df57d41c735d5ddacfe2160))
  - Merge branch 'feature/issue_220' into develop ([82bcb2a3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/82bcb2a3717c1d47af7b8b1545754be848bfeaf7))
  - Merge branch 'feature/UnitTests' into develop ([2cc4f10a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2cc4f10a730f17e58e9fb80c245103df2cedaf6f))
  - Merge branch 'feature/DocBlock' into develop ([4c7bfa5d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4c7bfa5dcd708bcf05454c517a0369b9a515a924))
  - Merge branch 'feature/Check_Dependancy_Versions' into develop ([1fba37f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1fba37f84be17227d9601abc8cad5d48ef0ea29d))
  - Merge branch 'master' into 'develop' ([22b18cb0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/22b18cb039b79ed138a559b63cb6f4cc925081f3))
  - Merge branch 'develop' into 'master' ([b5aeed51](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b5aeed5150647f23e71c572a150c15d3921886a2))
  - Merge branch 'master' into 'develop' ([442d841b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/442d841be74f6834f1b9010688590b264eb978de))
  - Merge branch 'develop' into 'master' ([04c93fab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/04c93fab743235ee1f202a060cc2d33d04583bdc))
  - Merge branch 'develop' of nxfifteen.me.uk:nx-fitness/nxfitness-core into develop ([355545e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/355545e1761a4fb8195e9e794465966172314fa4))
  - Merge branch 'develop' into 'master' ([592c2b22](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/592c2b22e24946eb4eea618bbe5c4a432aa7fa87)), Closes: [#90](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/90)
  - Merge branch 'develop' into 'master' ([5aeab80d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5aeab80d6b4656b6a367a273ec5de5f6639aed03)), Closes: [#96](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/96)
  - Merge branch 'develop' into 'master' ([931b5079](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/931b50799eda94f04a3db69dfd49cc7b509e86d5))
  - Merge branch 'develop' into 'master' ([57199ac0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/57199ac028fe1c4204fee0c88b002321d58d7ab1))
  - Merge branch 'develop' of nxfifteen.me.uk:nx-fitness/nxfitness-core into develop ([70cbd001](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/70cbd0019754ec4c71e4b08926cd6c77aae58979))
  - Merge branch 'develop' into 'master' ([a37758f9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a37758f929b8d4ab2ffa01de9abec3c123cbd163)), Closes: [#108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/108)
  - Merge branch 'develop' into 'master' ([b5332387](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b5332387e40df26c2631e031faed5b857f29f095)), Closes: [#106](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/106)
  - Merge branch 'develop' into 'master' ([1b0423dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1b0423dd7b8993052b232a4e9df191dc4affefce)), Closes: [#73](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/73)
  - Merge branch 'develop' into 'master' ([f24221e9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f24221e9e42b9f7bfe9efe5c10bcb7c77cb339c2)), Closes: [#103](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/103)
  - Merge branch 'develop' into 'master' ([766e29f5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/766e29f51087cd31aaff4826face2e54fa59ef11)), Closes: [#67](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/67)
  - Merge branch 'develop' into 'master' ([458cf745](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/458cf745deb50a20b3954f9d04082256613001f9)), Closes: [#70](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/70)
  - Merge branch 'develop' into 'master' ([208830d5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/208830d5a21534d8910acd3b9eb82cf8e8fe9570)), Closes: [#69](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/69)
  - Merge branch 'develop' into 'master' ([d0c7a858](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d0c7a85862777edc8c78ca58fc65b1ce6a93a69d)), Closes: [#68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/68)
  - Merge branch 'develop' into 'master' ([ed7c6038](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ed7c6038812c1a3e16f79dbaf9d6e629888c7a4c)), Closes: [#66](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/66)
  - Merge branch 'develop' into 'master' ([4c4dddbc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4c4dddbc39ed19c443c7bb26827b35cf1306ef33)), Closes: [#65](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/65)
  - Merge branch 'feature/phpdoc' into develop ([a3e6d4a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a3e6d4a8f18440e97c36b041538e850c82cbc204))
  - Merge branch 'develop' into 'master' ([0f6f2baa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f6f2baa7dacab19abcf7ef0b4a9baa4fa1f6632)), Closes: [#59](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/59)
  - Merge branch 'feature/get_sentry' into develop ([5f3b3047](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5f3b304750c413fd410989db20cb70ae04a08249))
  - Merge branch 'develop' into 'master' ([f79e4a47](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f79e4a471c5bb5b538aaac658311a1ccdb56fa2b)), Closes: [#48](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/48)
  - Merge branch 'develop' into 'master' ([e402798e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e402798e24ac2a13d4b604adfc2003c1f4521ab0)), Closes: [#47](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/47)
  - Merge branch 'feature/minecraft_woo' into develop ([e487b77c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e487b77cd49300334ba3b9088e95010e949f981d))
  - Merge branch 'develop' into 'master' ([a9b5c676](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a9b5c676098fa36456571182bf5233f7b19bebb7))
  - Merge branch 'develop' into 'master' ([94f57be0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/94f57be0a2221a2e3e0aa5e9839951070c0ab3de))
  - Merge branch 'develop' into 'master' ([2863b74f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2863b74f4a1ff7f204efd7a90bb300aaabff54cc))
  - Merge branch 'develop' into 'master' ([5263c2c6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5263c2c6f8e51fc51535736a93805933e9f21378))
  - Merge branch 'feature/couchdb' into 'develop' ([6044c400](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6044c40018de47fb802f5685714c4dceb9982028))
  - Merge branch 'develop' into 'master' ([8f79f24e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8f79f24ee7bc6f25dc2f1b77b3427240cb43547c))
  - Merge branch 'develop' into 'master' ([607b8697](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/607b8697af9dc2a4bfed75eaa970193b5239affd))
  - Merge branch 'develop' ([3e9db86a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3e9db86a309cc8706123e4a39d0edead30ec7677))
  - Merge branch 'feature/hearRate' into develop Closed #16 ([9ccf96ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9ccf96ca05599968280760a9f0b8bf9638e351ef)), Closes: [#16](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/16)
  - Merge branch 'feature/OAuth2' into develop closed #3 ([0bec54df](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0bec54df5236d733bf5745ece995da1e93947378))
  - Merge branch 'feature/issue_1' into develop Closed #1 ([9fa7beb8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9fa7beb87772d8b8c5e7af5d8c84a3633ab7b478)), Closes: [#1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/1)
  - Merge branch 'develop' of git.research.nxfifteen.me.uk:nxfifteen/fitbit-nodejs-api into develop ([1289a100](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1289a1003758740841259687de125a5dfc493825))




## Other Commits
  - Added depednacy checking test ([1f9f6f3e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f9f6f3e6eb9ec17989319749ee519525296e362))
  - Update .gitlab-ci.yml ([831b22b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/831b22b46d245816158dc4a7c22e655927f5f080))
  - Update .gitlab-ci.yml ([ef948f13](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ef948f13962cb4beff507ebba0e26bfec86d6076))
  - Update README.md ([a1460a9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a1460a9b9fb53bc0b002e618aa701b701badfb97))
  - Update README.md ([2c7757ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2c7757ee14aaaf2fe31b04562d5dd9888692cc4d))
  - Update README.md ([faffb4f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/faffb4f25a32cc17c1674fc2759d11066dcc3736))
  - Update phpci.yml ([415bcef4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/415bcef4f8d0a2e85e9ca453f7b90485e8071343))
  - Merge remote-tracking branch 'origin/develop' into develop ([7bdedf1d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7bdedf1d4ce59bf92f17fddc49209bb446227543))
  - Rearranged all code style ([cfac5c39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cfac5c3918e24b82cbee20578a5a981f17f90c28))
  - Added config.def.php to ignore list ([fbd49c18](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fbd49c1894292afcf3bdf44f05b51927d4389405))
  - Updated Nomie tracked group name to NxTracked ([d9662f32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d9662f32e29e38435b16f41c34118a206312eaea))
  - Updated streak calc ([0bb58738](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0bb5873867bd14a55433896239324d907f0e214a))
  - Updated streak calc ([404893b3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/404893b344ec8420499f80e268eddddf69c017e5))
  - 0.0.0.5 - Updated DB structure 0.0.0.6 - Populated tracker goal completion values ([7681c22c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7681c22c9e06eb5248d293156addc4bfb6459049))
  - Added commented out debug to print what upgrade functions are required ([81e18dd2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81e18dd27e2c93f1dbd059c8ca81e361c8ddc473))
  - Added checks for missing values ([7d1e2f8a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7d1e2f8ad4f80e175c0996431a6599a7192d8d39))
  - Added catch for missing Nome DB ([3f50aa4e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3f50aa4e9617a56d192134105906aca6ef8d7b53))
  - Better information for Tasker ([a400a351](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a400a35143b6d165a444bfbc3680f47a526c7afc))
  - Allow multiple similar/same awards, but only for one action ([78c3029e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/78c3029e8a12b8fc892b69fc25e55249baec4cda))
  - Updated with new DB structure ([8e3d4726](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8e3d47260f67b4d995bb332b3fd7b4f0246a9bb7))
  - Updated tasker return to support new DB ([e1412076](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e141207612820b7b947cbfcf1a936f04e29d249b))
  - Rearranged award class ([84b2b0b3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/84b2b0b30e34f680c1ba92dc1a999545f1a1a1ff))
  - Send more info to award class ([e7198ff7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e7198ff7c4b70e40d15336b25c4eb49a3ee1e992))
  - removed global check infavour of API triggers ([f3833fed](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3833fed40d824017c1dce016d6dbfc3eb41adb6))
  - Added award checks after API pulls ([c9837998](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9837998b33c141c6ee90385be942c04de59b748))
  - Updated NXR spacing Moved public functions ([0f87d3e2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f87d3e235fa8335e25f315098ab7771ac576a53))
  - Check if is already array first ([ac064177](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac064177ce44614ae7b42316a1fbd068bc1adce6))
  - Arranged rewards ([23e654c3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/23e654c3620f2c4dccbc2787340d29a79a1bbbe0))
  - Added minecraft rewards too tasker return ([2eeb7bea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2eeb7beaedff972cb55a16b765f0e6f65176cda5))
  - Check for nuke awards ([2494dfe6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2494dfe6c6370f7822783c5a0a74e5d6ff2abe64))
  - Added upgrade function ([a6a724c4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6a724c42d69445675072ab42750b6072291e3c0))
  - Updated JSON to support new reward calls ([33990322](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/339903223ea56a40e7c5aba40060d5dce78772f6))
  - Check for rewards after each pull ([0c54462c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0c54462c9261152246b597eed50b1507035f35c1))
  - Added Minecraft WooCommerce bases reward system ([be3e3420](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/be3e34208e5e626201df02e07cea7505cb502ac6))
  - Merge remote-tracking branch 'origin/develop' into develop ([acc221f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/acc221f2320cab7dc1b099267b6d157f0aa46443))
  - Moved nomie url to variable and activated session login ([ebb4aece](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ebb4aeced1e6e6fabc40c4e31761b897e215f35d))
  - Removed debug ([9035c9da](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9035c9da2c671edf7623d038a5ba20b951741890))
  - UTF-8 aware parse_url() replacement. ([b3bd9cc2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b3bd9cc23274fa7fd432752bee140a835f7ef5d6))
  - Fixed error getting heart rates across date boudry ([1ffc74a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1ffc74a8d6d40bdbe28f2603b2fa9a0c1ac9da4b))
  - Devices can now associate with more than one person (scales) ([5ba69da5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5ba69da54315bbbe70af7879f26fbff63ab8f493))
  - Resized small images to 200x200 ([910e92b7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/910e92b717f3422680fdb547c8161e69447e1962))
  - Added journey leg ids ([57fb57bf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/57fb57bfce013bef2984624e4bc029037997bca4))
  - Updated return to support new DB layout ([ddaa5ab9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ddaa5ab9feeeea6e64e263f58c4684031f5254c8))
  - Updated to support new badge return formats ([26cecfbe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/26cecfbe2eb4e55d50e65cd4f97ca05cec62abdb))
  - Moved todays details into seperate array ([aa63163a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aa63163a261fa08f371fd019fdd29f708d2d08be))
  - Rounded off distance figures and added journeys into Tasker return ([604a8e2d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/604a8e2da6c2e7ef94e4149597187f72014ab603))
  - Sorted meal return order ([b024fa9c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b024fa9c513c53ac7f363c8c3f42e1852539c066))
  - Pull all activity including auto detected ([a50bd500](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a50bd5008005b3a8318d84e98c2ab7f0696847a3))
  - Return info on device charges ([14761f14](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/14761f14f06106c172247ef28c0c93f4253eab0e))
  - Added record of device charges ([43bd7b91](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/43bd7b91f659a2c7df27a6a7a9cb4f25c2635968))
  - Seperated streak into new function ([76821b8b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/76821b8b8617c2365da9be54940bbd6816704025))
  - populated time field updated cache logic improved api_query return format ([1308f24d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1308f24dc98f2c4e6ad6994ae1cfabfe1d7dc966))
  - removed start/end fields ([79e90b08](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79e90b08d991fb1dc347359137c012aadae883b0))
  - Added start/end and time field ([556e566d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/556e566df5a52394b093d49b11101344b9e029b6))
  - Changed debug column encapulation to ` instead of " for better copy paste ([4de29280](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4de292803cb9a1059ce6305bd593af1578d7e29c))
  - added raw number value for prev items ([e08d5fea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e08d5feac86e080453f8b7389beec66fd4474c69))
  - Removed icon translation ([48ee3144](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/48ee3144ecd0e75fa5801444617ceead53058b28))
  - Fixed a bug - once a streak started it was ended on a date in the past ([0c2c1301](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0c2c1301565ff2414ac47ea485e296465f3cdaca))
  - Added reference check on tracker for supported features - disabling floor for Alta or Flex and heartrate for devices that dont support it (or the user isn't the primary) ([6c3219aa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6c3219aa6572952faa80eb7543cac5669c7acfff))
  - Hide nomie trackers with sort values lower than 0 ([1dc84559](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1dc845594d35c3b0609b7f83cac2dba6d1ceead8))
  - Reduced mark point for body (7 days) and food logs (7 days) ([4525fac7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4525fac7050f2595735f73a4cdfdce1a22b348af))
  - Return data based on MySQL records not nomie database - perform calulations on data as well Updated display to support new returned array and changed grid size ([0f2547dc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f2547dc96b9e8fa148426642af6776b7fc18610))
  - Added nomie events to mysql ([67322009](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/673220093be51ebd19702f97cea0d249d783d42a))
  - New users get no improvments and min/max is reset to users current goals ([21264f78](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/21264f7832336982f2cb8572f0d09b1b87b782e0))
  - Sorted supported function by key name ([75da6797](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/75da6797821ea1cc4f369b6040f61b41ab781a0f))
  - Updated join statment ([750b15a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/750b15a7401d7f3c37dbfb804f173a07e6ceede2))
  - Ignore log archive ([81edfcc0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81edfcc0bdad75f6b33c3a3dc09ecd16832afe68))
  - Whitespace ([1978c790](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1978c790b51611dd13201039d4d2abab34b72f6b))
  - Removed test user from update ([3d822f61](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d822f615f4db9907d8c1ef2ee2db50d6ce471f5))
  - Updated update feature ([62d5544f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/62d5544f0145b553db61b61ebd30902faf04c17e))
  - Added dev check to nomie tracker ([fe0d0326](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fe0d0326ec5b83f064261dabc23806d0c7a981b1))
  - Added initial issue templates for feature added in GitLab update ([7bef6f7f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7bef6f7f97699d5010691e2f5f82956c58023bfe))
  - Added min/max feature to evolving targets ([3bb6b1d6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3bb6b1d6bc1d8cccb8a4efbf6e059e4cdbd4b2b0))
  - Added array key check ([8dd6f4d0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8dd6f4d0c35b3b25be190441f9ca4e998017fb12))
  - Commented out strange dud line ([3ec82709](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3ec82709b0daaf6d07e49a7664b0b8fa87df5ecd))
  - Added nomie_trackers cache file ([3d721fcf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d721fcff7ab3811b0cad55c7aa3ae1c66e8a061))
  - Converted Nomie requests to nomie_trackers ([cfb7cf0b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cfb7cf0b51173cd7f21af2bd4fe35ab058661c44))
  - Added Alta images ([85f4a672](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/85f4a67299c9c1e266da302cc60a63f02cb456e0))
  - Closed issue #39 by including new database table ([7220279d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7220279dee2eeb80348ce349872d33c6f83c8282))
  - Non friends error message used ([568c8d09](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/568c8d09effcf56fda4bfd0475498c89372b19c0))
  - Function to store user settings ([5442d39b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5442d39b0193aa0d93588c230078e8b1edf56484))
  - Stored leaderboards ([7bc9c984](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7bc9c98499b8add5c82fdece3ff3e07b90e9da3f))
  - Updated README support files ([102aed36](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/102aed36ace8c1482dd1735334252002563aa150))
  - updated ignore files ([f3ecccf8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3ecccf8a9388d8b962f4c752ed0d5bfdca92046))
  - Added query date ranges ([8075c165](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8075c165b5ed826dacc0bfbf2c8d1e14c83f8a59))
  - Check start and end point of TCX files and mark as private when close to home point Only include GPX path on non private tracks ([c9042ffb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9042ffb8d098a42642a54eaf9c9e3733e315dc0))
  - Better URL support ([c403dd05](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c403dd05da3f7a595f7faa62406dab570dc3dba1))
  - If user is signed in ignore cache Allow XSS from dev site ([d842c5f0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d842c5f09f3a059cfc456022f5dac80fe42851ad))
  - Added precentage completed to title ([3b9979f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3b9979f839d9bdde94fbf41f468d940a0fbf773c))
  - Error checking for when no steps/floors/active minutes were recorded in the last week - defaults to max ([87a7d645](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/87a7d64522cf19002dbf272939f8f703a110b718))
  - Updated query to search on username and varibale key ([06fa2030](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/06fa203058b415b1b7dd364007fa2a675daf8f62))
  - Check session variable is present to avoid errors ([b7f24d26](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b7f24d26c26195bc1e02fe75755cd1f53347932e))
  - Updated to time out session Load config files into session Correct for paths ([e1f32388](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e1f323888f93439495bfc02d60a9828c22988a38))
  - Only save sesson if user is valid ([39cc657c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/39cc657cce9e5426ec1cbde9a50d8b9359d167d4))
  - Added variable to session ([f7c0dae6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f7c0dae635699ef9ac6b765783ca37d8f476e341))
  - Added helper function for valid user ([c53210f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c53210f3797d63f185cb11e80be8c238e69e096d))
  - Updated user settings table name ([1cdb73fa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1cdb73fafcf4356a0c042ad01c63de3a7c0880df))
  - Renamed challanges to push ([740c85cc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/740c85ccc37c41708c558f0261bf9a3e7e799339))
  - renamed database tables ([6d48492f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6d48492f4be407e9e5890fe028e393207791ffee))
  - Updated setting key names ([4363d8ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4363d8aceaf799c309280ff295ba641573c26ee0))
  - Updated user setting key names ([a1595edf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a1595edfb3129ae7094b4950d63bcb1055366db8))
  - Moved user settings into user_setting table ([83c7ae49](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/83c7ae49aab010d838bf23137749df29a898d265))
  - Added user setttings helper ([af48d1a2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/af48d1a2489f3f630c4753395820bbb6626f2c94))
  - Added user settings support ([6552214d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6552214d2174ea03cfbf516aec2c300bc1249c7f))
  - Updated config names 'nx_fitbit_ds_' to 'scope_' ([439670d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/439670d33e4e72116b8f59be5cf89359ec32b560))
  - If value past to isUserValid is email address check that first ([653352d7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/653352d75b07b5032d38d7c8e77b783608f7d284))
  - fine tune user registration process ([9ee1c6ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9ee1c6ac484b81c205c04722de7e4c44eb5e32ae))
  - Full Database export ([8ed2b149](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8ed2b1492f83134cbb1525994c45c9bcdc32a5d4))
  - Updated pull defaults to TRUE ([58ca4108](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/58ca41087620a424fd024c4e78ea9eeb670ab982))
  - Fixed GPX generations No longer return a GPX file is the TCX source has no GPS points ([523d1a0e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/523d1a0e957b6c3d450f76a757017e60a46f7192))
  - Whitespace ([e2da0637](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e2da063743d01fa8e4c44155356e78a6dae16e50))
  - stripped first charactor from requested path if its / since it was causing array depth problems ([e4a890ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e4a890caec5b0719e4e11a9445f8202eb9e3257f))
  - Added in hardcoded support for development site ([f10868cb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f10868cb116825c6002558f1458296cd8143906b))
  - Updated log path ([bea50e98](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bea50e98f6f1f72559c143731414a12585a050ed))
  - Stoped TCX files being skipped out ([52e75371](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/52e75371f829002a1f75ed72846a46e6123e8078))
  - Added starts to rate warnings messages ([6c69605d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6c69605d09c0e53516441cd957a8842c473d64da))
  - Added free admin folder to ignore path ([7fc6beea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7fc6beea8d9fd61ce958585f09a8c1a6df6abdee))
  - Updated log outputs ([3a6af7ec](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3a6af7ecfd227f2c7d5c3b7953f63f5e249f3dc4))
  - Updated data returns to support new Activity Logs in #20 ([92e405cb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92e405cbff3f85bf89263dd49e1e6a6983db0932))
  - Format updates ([583ecbf1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/583ecbf12f20490d08502d2866f26727fd4f7975))
  - Updated NXR function to support printing on same line Format updates ([8d2309d3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8d2309d3531c5df54508eb114ce771a6ecadbdf1))
  - Added owner support Now owners can use different OAuth credentials to everyone else - Allowing for a fitbit personal app while still supporting normal server features for everyone else ([06ce47e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/06ce47e19dc39c81289d071ca84c22f65a71051b))
  - Allowed functions to tell pullBabel they can support API failures Added failure support to HeartRateSeries Defined not cron is run outside CRON job ([0aedda0c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0aedda0c15b90c4b2e3af9bc4a20676110ad175d))
  - Updated warnings and code smell ([ac207f87](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac207f87ac3417fda1f78d2d0a9a846e170d09b9))
  - Updated code formatting ([c8c24f66](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c8c24f66c8caaaeee8e368cbd5adff1c2e17df29))
  - Support creation of new users ([27908e38](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/27908e387e908a19535bda9668318cee5bf7d477))
  - Added new function to create users Made pullBabel public Allowed public access to set user Access Tokens ([5cd149f2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5cd149f2df9f301b0b50fc1e1e51217aad690d5c))
  - Valid user check return user fitbit ID Added new function to search for fitbit ID's from user emails ([71800731](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/71800731aef0ee3a9a96b65e828c7f6a5d9e84de))
  - Updated Tasker outputs ([30a472c6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/30a472c6486803d2dc65a5702648691275f7c919))
  - Disabled heart rates due to API error ([1f50babd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f50babd48565713319cf2155a9bcdb0708c2457))
  - Removed cron print outs ([cb1cc520](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cb1cc520cdb79cb31729a4c7e5d360563cec6fb4))
  - Changed end point to webhooks - with legacy support ([8a8b3900](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8a8b3900ed046b651b3c772220cf0b138994f6ff))
  - Resolved cron issues ([63934e46](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/63934e466c83e8b225d0ba50f34f0f91cb879fbe))
  - Fixed cooler ([6ba2de41](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6ba2de41d488b1ce805a324ac2838f0f53f58a40))
  - Quiented down all output when run thru cron Fixed goal calcs ([725f87f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/725f87f8c4b381f723554cd92c17b29e10b9141d))
  - Removed command line output from index pages ([594078eb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/594078ebe64610dde758470211998782dc8ea187))
  - Reduced log output ([dfd7c5e3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/dfd7c5e36460f7c56650cef387f37f1a8f128f44))
  - Removed command line prints from cron action Defined if run thru CRON Quiented down isAllowed ([91b8155d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/91b8155dabcb621aa1d7f76bf43873ca18f078c0))
  - Added function to delete OAuth credits when they fail to validate ([8eda60f0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8eda60f075e367be9e807f09562f4211c2d38d10))
  - Added checks that OAuth setup completed before adding user into queue ([bf1b42f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf1b42f340b3f37523b0e7f69e09eff68f68cee8))
  - Added function to delete OAuth credits when they fail to validate ([e3a9fceb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e3a9fceba5c451b10a70ef4b0a3805614d0d6379))
  - Closed #16 ([529dfa3f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/529dfa3f475d62cf6cd12d21b829a72114a31f7b)), Closes: [#16](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/16)
  - Added helper function to test OAuth is setup ([e0c38f13](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e0c38f132959e51374fbce0c310c4e37753fab3a))
  - More logging to watch for subscriber validation ([a8135a5c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a8135a5c73c898556cf092ee5e6432a33863917e))
  - Closed #19 ([baba254f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/baba254f0f517919514cf1ad8838ebbdf306ce2f)), Closes: [#19](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/19)
  - Started #20 - Working on some mitigation by blocking this function after go live date ([781d6970](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/781d6970c7d4b5a2959334b5c01c31b8d21ea587))
  - Added debug output option ([8390c1ae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8390c1ae3126eba63b3ed1dca9f8be426ea37b91))
  - Code cleanup - removed all warnings ([c2162005](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c2162005f14cfc2dc7d2991cc20a5c0cc31d4e94))
  - Code rearrange ([b56b28f0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b56b28f09373eff8a72b3be21374bc3f44a31764))
  - Reduced database calls to settings ([0fa5519c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0fa5519c132507254c33a665a2cfbd5e6e7618ff))
  - Closed #18 ([652b4715](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/652b47153138851597a852978794e3eec305fd44)), Closes: [#18](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/18)
  - Closed #17 ([c9063a4a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9063a4a52c8910fd43c2181361c855a1cac569b)), Closes: [#17](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/17)
  - Closed #5 ([0c5b1f4e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0c5b1f4ebd27a27f27bfcdad3f0dc79c763a0769)), Closes: [#5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/5)
  - Closed #4 ([99e743da](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/99e743da49f2bd9e04a5b4d9de01522ce7db2ec9)), Closes: [#4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/4)
  - Closed #6 ([23b8a4d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/23b8a4d43f0f2773c586dbc176b96cd2bde8ae19)), Closes: [#6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/6)
  - Closed #4 ([77892dab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/77892dab32b3560df9d91357928f20bfeef8ca6f)), Closes: [#4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/4)
  - Closed #14 ([aa8b0ca4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aa8b0ca440a1ccbbce66d41e93051bed2682b319)), Closes: [#14](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/14)
  - Closed #15 ([30d43a23](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/30d43a23c6cd21df2756ba10a1a73a98106870cf)), Closes: [#15](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/15)
  - Closed #13 ([1319f5f8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1319f5f84df63f62841a98db87bd7950191bb4b4)), Closes: [#13](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/13)
  - Closed #12 ([a8c004a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a8c004a7b21fbbc6fd339d4bf04e1fc45772dcd4)), Closes: [#12](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/12)
  - Closed #11 ([3f080335](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3f0803353947ee6b61b5deb38c2bc54936cbc288)), Closes: [#11](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/11)
  - Started #11 ([aaf3109c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aaf3109c82bafea0f70a7c18a3e3c9a9ab76dd2e))
  - Closed #10 ([cef61502](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cef61502abb9c66d5091e5b59047a6ed2052cb7d)), Closes: [#10](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/10)
  - Closed #9 ([b166bfed](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b166bfed5889455130e7444cee473e7eb1e33af4)), Closes: [#9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/9)
  - Closed #8 ([fad9b871](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fad9b871970550da302649419fe3a8424b0652e7)), Closes: [#8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/8)
  - Referenced TODO's with Issue tracker ([cbd6aa68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cbd6aa68b17c0fc5c588025b7fa2f2f932fed065))
  - Added subscription end point validation ([4b582443](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4b582443814a89139c5a1481b035fec5396a4140))
  - rearranged code ([81f5986b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81f5986b44cf1fef008a195573c4ac22b2f4b3d2))
  - White space ([86408f2a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/86408f2a73fd5510ab58950b8d269a49327becbd))
  - Updating last run to database ([aec3a5fb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aec3a5fb824d05c4208522f5499603f97b98b92f))
  - Removed conversion from XML to JSON ([b9d7bd40](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b9d7bd40627043bd295bac209e04757cafeeb116))
  - Added nutrion to default scope ([9de0ff39](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9de0ff39260202948b9c55b8818c3e745d36d376))
  - -> Migrated Calories Goals ([8924ff23](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8924ff2320fcae5311907cb68cde88c171a29827))
  - -> Migrated Leaderboard ([6960c36b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6960c36b7b5470ba0b71e24e8a5f327843a53a82))
  - -> Migrated badges ([073a0012](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/073a0012eca883640671fdd90b744154fe10778b))
  - -> Migrated Devices ([a2a187d7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a2a187d798609c5eb8026f34f72cd1f02b0c1ec8))
  - Ignore cache folder ([f3b20850](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3b20850df866eda67781f4f50a66d3b268ed721))
  - Setup for new library calls -> migrated Profiles ([72b0017a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/72b0017a558b722256a2407fe7d23051f884344d))
  - Removed dead debug line ([1b6cfb46](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1b6cfb460a2c1d9f993a2e0714f998c5dd164554))
  - Removed unneeded files ([b625f792](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b625f792ff24c1900dbb965efaf39674429cbfa7))
  - Updated call to Fitbit helper scripts to remove old library Added function to get, validate and set user OAuth credentials ([19554c05](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/19554c05f1610fab55c13f3433d655c0357d8536))
  - Ignoring log file ([b5f4bd27](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b5f4bd2774b4b73bd97bbb070ba722a2d6e2ff77))
  - Implimented OAuth 2 authentication logic All completion points pass back to admin interface Added all other logic for landing pages ([e476edb6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e476edb699908e7fecafae72ddc52d79703a6f40))
  - Cleaned up subscribeUser class calls ([b69df9b4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b69df9b46dd8bd4f0c68895923a4729997f6f0ef))
  - Updated to support sudirectories ([d43ba630](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d43ba63099f7034b00ded35d2ce6bbeff443d09c))
  - Included composer autoloader into app ([1bd82d3a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1bd82d3ae8e265246169bc11477fb0c98c821a09))
  - Updated NXR function to write to log file in install directory - if file system gives permission Also output logs when run from command line ([c83da4db](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c83da4db95741125aa8948c6a3fbde3d12d6f0df))
  - Updated spelling ([03e077a4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/03e077a4b886ee8d3a4828403ec2c48794b9af2d))
  - Updated database structure ([f0443ae5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f0443ae5c6d9b01ac0f76dbed47a92197ab0265b))
  - Turned HTTPS on by default ([13ce65bc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/13ce65bc249f7fbcfce90445b8cae857a341be14))
  - Moved SQL files into hidden folder ([0ce7cb96](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ce7cb969a1acc0e723e08f291155ef559c3d312))
  - Added error output to clients ([52bb4c14](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/52bb4c146b4e28eeb374328b4b4c07707432ba56))
  - Added unit save support ([4d42c9c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4d42c9c96eac38bb5e3eb79c1faa58fd9c7dc664))
  - Check before declaring nxr encase already present ([382a5816](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/382a581691adf06e5372c174407ba6842b0ff246))
  - Removed comment boilerplate ([fd11b297](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fd11b297fa038fc86e7015804f46bdf06ae3cd57))
  - Removed executable permission from files which should never have been executable ([960cbb3a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/960cbb3a89190a8830be422a4fea73abeed18edf))
  - Updated BMI calculation ([beb21573](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/beb2157325b375e7d53cf6d6f6c2c6fd6ad626df))
  - Updated database ([d75bd6fe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d75bd6fec44021179331c535764b04ca731c3a72))
  - Added raw output to steps, floors and distance ([e76682a2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e76682a215bc9bebebb5d94273906fa92b93428e))
  - Ignore admin production ([1c4e5836](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1c4e5836d4642a3ba07ac969e0a8ee740daa146b))
  - Formatted code ([3304404c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3304404cf6645b61853683fe193e8277998830d3))
  - Removed dead readme ([864178dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/864178dd87a6eed550cb89dbe892359c8af7fefa))
  - Ignore all TCX folder ([93eb1526](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/93eb1526733322f7711614d8068fadc885c3a504))
  - During challenge days set goal to challenge values ([ff9bc9fa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ff9bc9fa070a0486c5eec3e5555ecda123579779))
  - Updated sort order for activities ([6a43f675](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6a43f6753641e9d9a1cf47e0adf718319c302957))
  - Formatted code, small updates to calender ([b162624a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b162624ab1af6005e4338fecb793e9c83603ac3c))
  - Updated cache clear triggers ([ba1fd76e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ba1fd76e74d14eae2aa1267dbd1d029a0bd02286))
  - Updated event text ([abd683b6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/abd683b616720c80de1144e3a2c5ee67abdaeb35))
  - Fix for sole returns ([0679d87d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0679d87dcbeb717973ad4508f8084706e20e4995))
  - On food subscription clear cooldown for water too ([33e4f2e8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/33e4f2e8884b38f2b5ed9401cfd5b16c3144c498))
  - Number format calories / todo steps ([33596614](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/33596614db28ddd64c1af0f6946d464b79405b2f))
  - Updated name outputs ([71863ab8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/71863ab8083dc97181b1d50c41ad29c3c584957f))
  - undownloaded goals dont trigger cheers ([5f6a5249](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5f6a52496ef4f586acebdbf77294a650ccdaec5b))
  - Added steps to return ([92a38149](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92a381495cb94524910b2a5f464ac6749fbcf167))
  - Updated link outputs Deleted test Map files ([5fdce47e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5fdce47ee3ad4b4518887c4b0b177cffc1a4b42a))
  - Generate GPX files on first call ([38ad982c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/38ad982cfd0c65509c1943dad8104bd4caeadd59))
  - Updated map a little ([056ddab4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/056ddab410a40496a917cb150be8d92f4cc205cf))
  - Stopped caching empty files ([21fcb7e7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/21fcb7e707a5c786620b821ad0fb7699c879aefd))
  - Added cache busting code ([af706d0a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/af706d0ac09dc67f105bf33b2e22d4f9d51bb680))
  - reformat ([366d50ae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/366d50ae190eea5dee0a7b57165e3dfc8938b72c))
  - Added initial map idea ([3d2c7372](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3d2c7372b7b6aa5aa4053e2a758e9dd5016cffee))
  - Updated GPX style ([064d954b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/064d954b44a78d512f99ec09df91106503d0be92))
  - better handeling read errors ([f14fb9e3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f14fb9e322eef2c062177f5bc4e8fcf39b3e67e2))
  - errors are not cached - untested ([851f1efc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/851f1efc3afab824d8af79a525003d086784695c))
  - rearranged cache order ([8ad17c34](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8ad17c3415d9f4765fb095c53c5aeb6b7934b133))
  - Added TCX into cache filename ([2fcbb600](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2fcbb60086dc1584f94597e5e54735f1ab6d52a1))
  - Added more info to return string ([cc9d519d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cc9d519d228e4c830292427b5fa7ca1f1c6e3cc6))
  - Added TCX to GPX converter Code formatting ([ee252e93](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ee252e931fa0c4c6307fbce836520db984fd5580))
  - White space ([5dac2d32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5dac2d32b7add54264ed3ee093194e0f1e6f12d1))
  - Updated to support leadership changes ([493d1db6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/493d1db6ee22a112a1a0a38db9a3736337789e12))
  - Driving activity not downloaded ([37f79067](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/37f79067e26517cf5155d535688f1efe1c61517b))
  - Added scores ([3dbec6c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3dbec6c73c6b23c63f03c899b4ba30a1f662f2f3))
  - Added tasker function ([ae54d175](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae54d1756193a19838d5c17e47a6ce963bd0ece3))
  - Added current stats to challenge output ([2ceb7df6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2ceb7df63e7110d3e5aa56c1d2c70fd8a804c44d))
  - Code cleanup ([e456a566](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e456a5661bc8f657169904b1acb687a53eca5268))
  - Removed command line prints Only set active targets for current day - stops current traget back filling previous week ([51078b60](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/51078b604a740b78b872cd60d2da722045702f96))
  - Set variable in class once created ([22cfe1b9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/22cfe1b908c0dea529f90943f225ea88a74f6e49))
  - Put settings into config class once called ([241290ff](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/241290ff37e19417844657489e709dc596a6db74))
  - Removed debug print ([964f8201](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/964f820177440cc9b7c837356bdea1633f5eb396))
  - Updated to suppport multiusers ([c9805abf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c9805abffff9f612db49db2730c70238cfa85aeb))
  - Keep folder in repo ([32c809b7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/32c809b75db8486dad8f6a23c7b5008af033b645))
  - Ignore tcx files ([e35a42fe](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e35a42fe073e39af49ef3f967bea5d54a19de13e))
  - Added step information into activity returns ([d77617dc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d77617dc211b4c72793b6ffa5a602988972df691))
  - Two day grace ([f4a829df](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f4a829dfb2b79cdc5e5719f2811c6f27e6dd1052))
  - Added mark when no records returned ([2a77553d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a77553d91f663fc641fb2b8e5292be97e4b0a5a))
  - Before cronning check its enabled ([410b0e9a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/410b0e9a184b043e8587960701534bd20f5f035a))
  - Updated Activity records ([05e43f3d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/05e43f3d686c0caa572c283200de80b8e6076441))
  - Updated checks ([e7394f0d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e7394f0dda3d647dd890281ccbdde314e1877326))
  - User and sys settings active ([ae246dc7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae246dc7a43e1e07b28fea0b3364da334f6d3c17))
  - Default to 0 not false ([95b68c89](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/95b68c898cfef243aa782e748f27d86de547ad91))
  - Added first step of user overrides ([3fd9caca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3fd9caca7628854bcc93fd129d1bb81f60bbe24b))
  - Added new error code ([6fafc117](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6fafc1174c4b9639febcf0630ffcc5e20c037abd))
  - Updated default passmark to 95% ([5448cd18](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5448cd18841d40b6ee9c1686d729847bbad565fa))
  - Updated pass mark point ([91e40bea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/91e40beac83e0fc7d355ac55741cb89c6e5f9bf1))
  - Finished adding steps to challange ([46627862](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/46627862df608fcf23e1a86129e90d9b96a34d4a))
  - Slightly working code ([39431e9b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/39431e9b7d5f6d7e4296684c41893dedb36c8e92))
  - Added steps to challange ([277a22cb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/277a22cbc6d05608db38341a1b756d438bd6a697))
  - Increased cache life time ([d8d19603](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d8d19603d314cc53866f0c8500ae9f29044eeeb0))
  - Added last sleep to return json ([2db77ecf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2db77ecf0f3b4f26d0c3ddf17785895ad9e861ca))
  - Fixed changed format into sleep log ([869c8162](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/869c816213b90d19a34fede5a600502016ceadd4))
  - Update code formating ([1b80adb0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1b80adb0b0e57907f8c4a6a6b8b1b0e0463a3298))
  - Added calorie goals ([f94090c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f94090c94c4e1a9ea7aa1a22df2e1c7dbbc3e6ac))
  - Check file is there before deleting it ([db78941f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/db78941f777fc07b69c9c5e221db3a4aaf08f345))
  - Updated food diary ([645c91ca](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/645c91ca6e8a626f6fa351dfeed8219d0e4f64dc))
  - Added date and debug to cache filenames ([1f393827](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f393827d943595970107b1f0d8f3a6b1f7e249b))
  - Reverted medoo to 0.9.8 ([a5cef619](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a5cef619e700a4e987fe26e0076978306cb921fa))
  - Added sanity check for no DB results ([8e148446](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8e148446d42025653b488ccc66dc84d1e648216c))
  - Fixed date range ([8f97c9ac](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8f97c9acef2e1edace534c770725d6959d83246c))
  - Fixed API changes ([bda76437](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bda76437ef2b5d7148cfaa499cc21a99cd3b1d67))
  - Limited progress to 100 ([726eab5d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/726eab5dfdc19debd1aad1c7cad5b5295f95c552))
  - Rounded weight and fat records ([22f5c3c8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/22f5c3c882065ac50f4d3d1c18a1d640ea053165))
  - Removed double limit closes ([2f0ada15](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2f0ada159199bc7f1d03fbb94ca9989d331d266f))
  - Fixed another limit problem after database update ([06e39b11](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/06e39b117a90a207cffef6dc98dbb00c51de7078))
  - Removed limit perameter after DB library update ([b041a1a2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b041a1a2757f7afef405780401ea1534fc36133c))
  - Fixed problem in badge array ([a10c0801](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a10c08012a28f9f8b6f109c9894f785ceddf9146))
  - Added check for blank badges before adding to DB ([7c0b4811](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7c0b4811f1cbf2014f5679b4efdaf8b6cfaf090a))
  - Rearranged output and added activity goal details ([3dfa549f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3dfa549fad98a2d95620d24fb7fdc3ac8991fc03))
  - Added dynamic activity goal ([84dd0918](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/84dd09185bbafdbec8e0ea9b312e0d8640e90236))
  - Added active minute targets into database ([008c2daa](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/008c2daa534394593e19fc4e30874f38935db7b5))
  - Added active minutes ([3b08622f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3b08622f0e72ff2e3046de23968ad0a392377682))
  - Added new datareturn for conky ([35bf7136](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/35bf7136d56f957145763adf1fca8600ffb93933))
  - debug output is not cached ([eb39b136](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/eb39b136999f67bf65f6ab5ad60ba7b920ac42f9))
  - Added debug option to output ([0d6aa679](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0d6aa67989cd7c8a130d2c587e7da24253a2b53c))
  - Added precentages to step return data ([f363b959](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f363b95966286a0218490380c724e87a73692785))
  - Updated library to 0.73 ([6f1d9a53](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6f1d9a53ffdc5f648eaaf9cab81a3e0569c2f68b))
  - Added fitbitphp-library git repo ([878ca8ab](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/878ca8ab9a3811995f8ccd9cbbad851894228fee))
  - Updated medoo library ([c60035c9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c60035c93ba4696a072b150a2b5f760632f672d8))
  - Corrected if logic in body when no weight/fat goals have been set ([d98257a7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d98257a7eb367135dad73a084f22c9af935b16ac))
  - Fitbit errors now report to NXR rather than console + code formating ([ae785359](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ae7853593adfea48ee8bfaaee0cc209f07af55d7))
  - Added user Subscriptions to profiles ([9a670b2e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9a670b2e9a9e0373570b21ad67997adeff47142a))
  - Restting Fitbit class for each job ([f835dcf4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f835dcf47ae72bf7eea5a18171f3fdb7d913f10b))
  - Added rest tag to fitbit library ([e9222b4c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e9222b4c08dc3e797a0c50804b99e0bae49bc4ac))
  - Set error lookup to public function ([b30a0959](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b30a0959596adbc7b63a63a7b7195f48ab12c12a))
  - Removed new line ([770f3d34](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/770f3d346e3e665970a85214a6516a1a6e06f669))
  - Added username check. all output logs go thru nxr userid is set in class when establishing oauth ([7ae05a02](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7ae05a02988f9cd833aad2830787a4d1bda7d097))
  - nxr past to log file rather than console added new error -144 when username doesnt match auth userid ([aaae0140](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aaae014070c213dd639eed0bbe5ca9ab635364e5))
  - Updated loggin ([67240650](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/672406507cdc2fe0a963260d610dbf29fb80a227))
  - Updated loggin Added queue run to function, after repopulating will rerun queue if time permits ([396e42ee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/396e42ee16e95ff254d9fad65c9fd0007fa06740))
  - Added function to return userId value ([b81ba035](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b81ba03543703116091297f9220268b85085e2ab))
  - Added cache control ([d6a4cfae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d6a4cfae9997a59b00895665e4d815b1e8aca0ef))
  - Updated SQL call to support multiple users ([388b5f88](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/388b5f88b7ecbefff2dd1ee0ee33de29ebe83a26))
  - Changed default improvment to 2% ([9b7429f1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b7429f1a6c5708febf99f302ec82f6eb033c50d))
  - Updated files ([ade78d4d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ade78d4dc94766bd0a33d4ba0d85fbff7bd5637e))
  - Updated lastrun marks for missing records ([ac30d289](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ac30d28925b1df878b39bc66a641eaabad19dcd8))
  - Added missing fields to insert function ([0ee54b9a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0ee54b9a8d386406a8c4e0010564bd5348cd97dd))
  - Added badges ([c8c71cb5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c8c71cb5153b9823f24d10d5af2de48935939e26))
  - Updated wieght and fat averages ([8aecfa17](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8aecfa17021327fbfde8bffd2e564211a434778f))
  - Added jounrey progress count ([09ff83b6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/09ff83b6af89fc8b008fd7d1140b9a9e819d7e0d))
  - Added jounrey features ([e8a87881](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e8a8788150fe270644af527ae8c2fc308ef5b323))
  - Added return for users without passwords ([84ad0d4d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/84ad0d4d834a4efd8b7245f74fa3c868f6474df4))
  - Added validity check to username and password ([b35ef4be](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b35ef4be107d47deb9b2b505c911e0c8223fb9af))
  - Added Fitness & Health Admin repo ([0f442c7b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0f442c7b710af52ea67f7eac1d7d209b508a38fa))
  - Updated thisWeeksGoal to take week Monday to Sunday rather than Tuesday to Monday ([2e9dec9f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2e9dec9fc24365052cd14b2548ba6e1b14c1c00c))
  - Updated challange rules to match fitbit definition of activity ([e61b8a3b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e61b8a3bfe6a042ed514e89079dfc4070a067482))
  - Organised code ([10bf8c00](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/10bf8c0087c586990186fcb66d47d34c4ca1d80a))
  - Added PHP Docs ([a578bd15](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a578bd15075e9bd3a75b621c85d45d697d53525d))
  - Dont get previous years unless required ([8290e107](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8290e1072b04983c511eff5d19a5456964727ebc))
  - Updated challange past returns ([1f37b72a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1f37b72ad35cfb68eb55cc0d8a245068c92e8437))
  - Corrected spelling ([c875d087](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c875d087462524596e506214e758d138fc912009))
  - Updated challanges ([03a6b01f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/03a6b01f68691830c7965780bb6545a613546e5b))
  - Always pull last 7 days ([93d47d11](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/93d47d11ab6e4345b428fbdc37cd7cbb10402635))
  - Added correction when goals for today are not yet ready ([24ced65d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/24ced65db0b8fd59273301b6faaeb4a543907520))
  - Added calender entry for start of next challenge ([e6a2b69e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e6a2b69e78ee2b242115576ac1973085114ff4d6))
  - Added user challenges starting, by default, on the last sunday in march ([900f2c64](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/900f2c64c8c00ed3292922c025c88297517d2319))
  - Updated json cache ([54caf0c0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/54caf0c02731f21d17e9baaf71bdd99e5baaf830))
  - Added option to return function data without heading values - ideal for fullCalendar ([448ac999](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/448ac999e243e666de81999c51049ef4e7d388dd))
  - Added new sanity check to tracking code ([9e13619c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9e13619c9350f12eb56068a4f27ea6797c7a9a9f))
  - Updated cheers ([cafad06c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cafad06c1a4d0d4ae3181963e36ed65f365af608))
  - Updated times text Fixed more less arrays ([a8d3bb0d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a8d3bb0d91e9ab84a864873a0fcbceafca74af1c))
  - Added debug line ([691e7277](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/691e72774363d2cd79b281dbd4476c1b26d7e03c))
  - Can no pull api_pull_time_series for more than 365 days ([13766cdf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/13766cdff7fd82bc47efaac379203ae0d33debaa))
  - Added cheer to step, floor, distance returns ([182de8a8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/182de8a866f747e82d549bc438bd6ac8b1c9543b))
  - Added cheer to water return ([5e309f68](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5e309f688e9e8d0daebe2a9f6219abe08811714c))
  - Added changelog ([fe23196c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fe23196c4458dabb71b7fd1b236365e1f0b505d9))
  - Updated README ([869cf5bb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/869cf5bb34510ab8173e4536905efb6b146a96c3))
  - Closed #37 ([320e9480](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/320e9480b3434f43b6bf8e35a56fe91aa8312a1e)), Closes: [#37](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/37)
  - Added check to see if any cache file names exist before running loop ([31c7ebb8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/31c7ebb8f01e4786720a10243a8455f1db6167b7))
  - Added map of cache names to data sets for issue #35 ([fddd5a98](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fddd5a98a03263bf4b973abece9035f8aa3c1575))
  - Added cache file names function based on called activity to support issue #35 ([36d90d8a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/36d90d8a8c0877c4453c0d11a4cf5683c9f8d60c))
  - Publiseied settings ([940e7e58](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/940e7e585b1f625b0ec0a22564041906f40f8674))
  - Added PHP Docs ([c1a97f76](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c1a97f763484f11dab1ba0343d98d3593cc0708d))
  - Refromated Code ([4e136ffc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4e136ffca209658c7e6c2487b1cc546b6352081d))
  - Added increasing floors goals ([2abfe3b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2abfe3b15be467021b1524ca049f788af1eac97c))
  - Added max target value ([2e0024cb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2e0024cb75cf86c9ed8308eda5044c316bfe5dab))
  - Added number formating ([f5da9109](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f5da910923931f56857a42c6e616e63eb1956bb0))
  - Returned current goal ([a475b39c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a475b39cbda07b9749ecc55db85826085bfcf166))
  - Added goal increase for floors and put into graph output ([138ead11](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/138ead11ff637821b7c42933bef2e1367c6611e3))
  - Updated graph returns ([813de882](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/813de88239a2531fd22e2254e5a72cf6d0929bf6))
  - Added return date ([bd7d31e6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bd7d31e6406cc34158869ace8d35e2360fbf55af))
  - Added return for goal charts ([6ecb2283](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6ecb228379e99e89453f890251db906296362be9))
  - Updated test file ([213e899f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/213e899f09394b98ce4a9dfa355b66fa64c2761c))
  - Added check for tracker before calling it ([84a92850](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/84a92850549c61d3413dd53b6b559beaa76d5d66))
  - Updated where to include table names ([642b7dc2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/642b7dc249b9219f8a3f960bae3ccabb9de0b029))
  - Fit int comparison ([b9da1424](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b9da14243f04d7f93d76025368b51b378ef8ff27))
  - Added auto update to weeks goals based on settings for improvments ([13e0f8fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/13e0f8fcdbb06c41b995ab05ddeb00c0903b2038))
  - Added step goal caluation and updated test script ([93318b1c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/93318b1c882e4ffacc884d93339e661dfa742f0d))
  - Added sleep log support ([298151fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/298151fcc15e0223e5f0dd5167b11f459a1b60af))
  - Updated test file ([7677781b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7677781bf5df7265a6b272bf3e2ff573f2bc2c61))
  - Updated DB ([76d63f64](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/76d63f6479edb6d337751ad67c2e5655a6f2a6c7))
  - Added new noun ([ca4ab022](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ca4ab0228138105344c0101fbcbd63163653e941))
  - Closed #32 ([cd1971b7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd1971b76dccbf830a16beff421bef20a6716750)), Closes: [#32](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/32)
  - Updated test file ([a84ae889](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a84ae889d4b9d78aff5dabaf6a7ce6f514270dee))
  - Added proof of concept keypoints ([974b1863](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/974b18637a4c98baacb591871715917856c3256d))
  - Updated test file ([ecac8960](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ecac8960f5f11c83eab318e48122c2828d2059ca))
  - Added missing function name to error output ([f6b94ce8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f6b94ce81452abe231c986256c96de93f4b5f3ce))
  - Fixed avg calcs ([e788c15e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e788c15ea2f2262ac12060eb018176b6d5d203ce))
  - Added override to time series when activities is called ([90e95b4e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/90e95b4e069ab26d8bdb696414d1efc710fa7b44))
  - Disabled IP validation as fails with varnish ([d234cf62](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d234cf62ce86309f096808eaca3edb73c1d01221))
  - Added subscription endpoint ([bfd15cbd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bfd15cbdd952323784c6931981ef1540ce7d0cd0))
  - Added subscribe to update code ([500de356](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/500de35671ae1a0472366260fe9f0a3a024af576))
  - Added subscribe id ([b9756d64](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b9756d64863f915e6902991de5309234b23e4b3b))
  - Added draft sub return ([3c5662f3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3c5662f3484402c357cc7b27e57498675a7f1433))
  - Added testing files ([bcd08504](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bcd08504e12be3f930b0535a84f828035d1fbd00))
  - Added avg calc to pull ([9b148f1f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b148f1f883821db14717f1fd78838799bf5fb16))
  - Added class to support updating database fields ([4cbd88db](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4cbd88db5b13df18d095948124207e7f707be1f3))
  - Closed #24 ([bf6b421b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bf6b421bd2e71362155b65abf6e372c7ae393a02)), Closes: [#24](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/issues/24)
  - Added return or zero when battery is ([286a3338](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/286a333845cc49d1e73ffd35438aa2151021695a))
  - Expanded #22 by adding alert values into return ([2cbeb5ce](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2cbeb5ced8252b8ef653f3a6289fc859fefb363f))
  - Close #20 Added user full name to all returns ([4d6a8b4a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4d6a8b4acd4a3fb0afab7ffc6585935d013f92a0))
  - Rearranged sort order ([18feaec0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/18feaec099760c3ff162a1d20ae2153a96755817))
  - Close #19 Added activity history to JSON return ([94f75f4c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/94f75f4cd232809bf26ab521c18fa2c3bae4cffe))
  - Close #18 Added device.type field to output json ([7ef0ff92](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7ef0ff92ab716a75230afed9c66b416c754d6447))
  - Updated requeue logic ([d6cd3ab8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d6cd3ab8c9b5b38a7124683e236f74a3253088ae))
  - Finished #17 Updated images and added battery precentage ([2a36e8dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2a36e8dd441f0f367ad80c9c0b0a0b9f8c707327))
  - Added PHPDocs ([d9b4735c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d9b4735cc00e89178d17448ca6f6d98c01ae296d))
  - Reformated code ([aaf2a7e7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aaf2a7e75b6f1a567d977f7038a80fe346b36db0))
  - Seperated missing record calculation into seperate function. Working on issues #9 #10 and #12 ([bef122a9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bef122a93ff6af306bd74185507791d955ce4f85))
  - Working on issue #1 Stopped adding blank records to database ([5ca6005e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5ca6005e5fc15bf2b972f26f11599c6cc5f70a58))
  - Added JSON cache files ([25a1c060](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/25a1c060cfa18ac46ed1d40dcfa646c7b158b427))
  - Added calorie deficit ([a8128633](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a812863389be2d187358b0eca36bd08a0f29298a))
  - Added cache busting headers ([f047e93e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f047e93eb49001dff4e4dd4772d2e1e44a575a95))
  - rearranged placement ([bd198949](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bd198949551af7b7b8045d8ae72ad13a7e76e92b))
  - Added cooldown erro alert ([671df126](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/671df126e4109354834f85e0310d4b75bc876a1b))
  - Updated index ([14727464](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/14727464ea639ecce9008a7fb07f89b73fdf6d59))
  - Updated index ([cd0c853b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd0c853b25f18ac92d8f5b966aab0a937b45c6ad))
  - Updated index ([9c78b56a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9c78b56a0d18c8109f914ef2da109bc7f19de608))
  - Added activity log ([3600e269](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3600e2692b39a1b70aa0debd104e87fb19d224d3))
  - Added activity log ([9b9e759e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9b9e759e3dfbcbe1acf946863f6f44150584f50c))
  - Update activity log database ([a31c15d6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a31c15d6fc05cc25077c83324ce377d51ded55b1))
  - added phpdocs ([38b8a84a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/38b8a84ae10cedc5510da7fab820115b510c70ab))
  - Added date to steps ([0e21228d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0e21228df8768b82ab7d911ea38c76eea560461c))
  - Added weekpedometer ([8434af1f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8434af1f05ebefbc3d755d294a44b7473ab1dec8))
  - Added user activity data pull ([167ef7c8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/167ef7c817b1083afcf710832ae364fb8db51564))
  - Updated device linking ([fc1a1127](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fc1a112721ed40748d81f0de077e86c579da0727))
  - Added gender support and formated numbers ([cb6042e9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cb6042e9081895331204975c1bee99295d6246fc))
  - Added better detection for zero returns ([3f9d53fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3f9d53fc3ab65b842df1abddd547c93aaf2e5a8b))
  - Added new datasets ([4adf51a1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4adf51a13c27167007be1af154d5285918f4b148))
  - better commenting debug code ([18de94a5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/18de94a52a084cf9457edffc7a19ea02dd269dd8))
  - Added device images ([e2729b00](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e2729b0048b7c0d618f857065f0a92ed143be015))
  - Updated return data to support new datasets ([ef28f212](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ef28f212a3e234b5a73a86264987b45a263cbab5))
  - Rearranged all trigger ([a7e71ccb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a7e71ccb3c6ba2e9c75a07211660bba102dab8e6))
  - Initial dashboard returns ([4504dda9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4504dda930c89a411f97ca2b7fd35999019c4d82))
  - Better HTTPS detection ([72d46e7d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/72d46e7df5562bff7a0d5e6dd8f3043d14493b89))
  - Added better tracking ([240112c7](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/240112c72d4652ee17056179f45d1ebddf99b155))
  - Fixed some errors calling tracking class ([82af73a9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/82af73a93ebfa8a77f448ea2339b832be8ab9b3a))
  - Added tracking code ([1c553fc4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1c553fc4dc98bd7fef10a12a9ffb93fccb1b3422))
  - Added queue all once a day ([2ae5af45](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2ae5af456e52361539f6a0b5cc342cd5b2488a6f))
  - Updated to only support json ([5272bc3d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5272bc3dfc2eb77c08f6dd06ff967ecc1ded18fc))
  - Past username to error lookup ([28dec5c3](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/28dec5c3c31d5302935caae72c3fc2f10f195d19))
  - Added hour cooldown when API limit reached ([283bcb3e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/283bcb3e4dacb451c1e4b5aa9f29e18f17e19f65))
  - Added more IDE exclusions ([f3a56b57](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f3a56b579df478f4e2cb9fcdb92919754b9dcf20))
  - Removed unused function api_getLastrun ([02e1de7f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/02e1de7f4872e7ae941ea1ad5c2c854939657e65))
  - Some warning clean ups ([95783b35](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/95783b354f2a911fb54836cae06b2ee51afcd6e6))
  - Shrank cooldown message ([b9418d2a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b9418d2a9309de41dbfe0c71a173d1bd18f7372c))
  - Added lastrun mark to activities ([5541724d](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5541724dee44f0be8933f72ea3d4c2570d44a10f))
  - Updated heart rate pull for JSON return and marked as todo ([49153201](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/491532015d1b48dee153833795c639e612900d38))
  - Updated leaderboard pull for JSON return ([1a0bfec6](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1a0bfec69ec4629efdeefad2832d060024026c5a))
  - Updated device pull for JSON return and look for device images ([946d5747](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/946d5747b117e89399747b4e021b3f7f78f2bc06))
  - Updated food pull for JSON return ([fd523441](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/fd5234411b9d4ede5f6f69492c120d4c9ced05bb))
  - Added commandline file to get dataset ([7ed6bbd9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/7ed6bbd9d78eed8e5fc059d92261252fbb69e318))
  - Fixed badges ([a935cadb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a935cadb4d4549b1b7b2c30734e53b524c2ada3a))
  - white space ([b34e1161](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b34e11615d78788aedef59dc2ddd3de2e97beb78))
  - Added better output arrays ([540f4808](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/540f4808767676f9f34af7b90f71ef5a196844c5))
  - Added commented debug code ([92d7977b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/92d7977be7ede6fbd0b6a7e1fd1b7837e8acfdb4))
  - Added food log call ([42daf814](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/42daf814f9df8366151eba38b522c1583e8cd8dc))
  - Added rounding to returns ([f33eb188](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f33eb18859ea4ced643fcfb4aaa8004ea9116a7b))
  - Added water and goal call ([3ca96fc1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3ca96fc187d036df45b0970497adeb0a906c5834))
  - Improved error messages ([a2e4a42f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a2e4a42fd767c943cc32051a0e993106afac11ec))
  - Improved error messages Changed timeouts ([b43acb56](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b43acb56acf31353bd090e1eceae5849a6c6f0cb))
  - Dont requeue all ([6e1c44dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6e1c44ddb434c03bfb9d8eb182c115472d3833da))
  - Extended run time and improved console output ([d21cafb4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d21cafb412fc43266f27cb3dbba7056234ef8786))
  - Added intial functions to return user data ([6a1d1710](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6a1d17104dcbbdca7ef4b65851078afe439f2e7e))
  - extended run time ([42c807fc](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/42c807fc5c50edd778e6dce67f887b62555e6e7e))
  - Added composer to ignore ([81ea637a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/81ea637aab236c0dde8c5713b5127b0ca4151f7a))
  - added better outputs ([3918c7f5](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/3918c7f5fa9dd5f9860788df42f3190bf8b4cd22))
  - Added check before adding to cron queue ([79f7c695](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/79f7c695cb59a0d991ee2ed79d074674dfeba34c))
  - added cooldown force ([c829faae](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/c829faae6e675f85b832331739e6aacdd3547b87))
  - Changed colon place ([b08033f4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b08033f41f6281e17a2936f68d1d3175c22514df))
  - Improved error output ([f8c01fa4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f8c01fa498a1a34512aba07b295c74eee6375275))
  - Added rate limit error code ([e5c41cb2](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e5c41cb28bf9714d844cee1b40f03878fc0148e4))
  - Reformated code ([d790f95c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d790f95c1b65a61eb1e6a0806501ef0fdf9dc32d))
  - Finished migration ([d3e6ea6b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d3e6ea6b5b188743445f3c6d3e8a65fdd293fdcf))
  - Renamed pedomitor to steps ([4cadb7bf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/4cadb7bf01605f141fad633cb42509fa08892548))
  - Ignored downloaded images ([85404ee0](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/85404ee0addd7e9350bfedac63c02b93badda69f))
  - Ignored downloaded images ([8fa1a697](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/8fa1a6974cced28771f59324a1779ed8e0efa96a))
  - Rearranged code ([aeaef481](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/aeaef481ce8d77bfe126437868b13795c112bd79))
  - Added missing doc code and arranged source ([747f6d33](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/747f6d33d927c99c4f4dbdbc51626ec548da4371))
  - Inproved debug block ([26c71155](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/26c711556fb6b06eed3dd10a63a7986ff8f34cb6))
  - Added water log ([a6ca679c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a6ca679cc59cef357d31d8070fefd640a72075ba))
  - Added heart reate ([6433e9a9](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/6433e9a9133640042fc9d431565f5e25016bd643))
  - Added body metrics ([ced4f61e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ced4f61e67a24bd18421311ac53bcd7ea9e4b65b))
  - Added sleep support ([a549fdbb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a549fdbb1319f781d5987fa94c41d8c8a68faea4))
  - Added leaderboard and calorie goals support ([2b0f8f25](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2b0f8f25fda851075f33b892e787d4b5faefd291))
  - Added new error message for missing folders ([b0ace803](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b0ace8036f8901ac9b848b727cdb7f49d9fbfe06))
  - Added badge support ([653c1f89](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/653c1f89dd3daf631027c08e7707efe57535b8cc))
  - Added 300px badge ([2953a9ea](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2953a9ea254db30747e1762a10fe57634e9817d2))
  - Updated size of name field ([a970bbc8](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a970bbc8e392cc7d25fd314106f4f5dd9f67a4f4))
  - Added default return code ([b78984d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b78984d4c1c194e869bd0d382896aff60cd7bba2))
  - return API value optional ([d778da31](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/d778da31ab1ac3ae80d3ff97d42d999df3ed5c9a))
  - return API value optional ([f8b7b65e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/f8b7b65efd3352e0a4258ac359701841a42a54ea))
  - Added sensible error codes ([a54cca6a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a54cca6ac09d5f291b75e8256471c3d6e9a4986d))
  - Added sanity checks for blank returns ([e774a074](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e774a07442d04080c41ce5c775bc5ff4e500481b))
  - Added support for true oAuth Added cool down methods Implemented profile updates ([2169013f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/2169013fc9057cacee874b431577526f034e58c8))
  - Updated method names ([cf96137b](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cf96137b749373ab7b33f4ab2dbd91365c435454))
  - Enabled oAuth functions ([1273939c](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/1273939c7bd8565881fdf041c72290b5683869de))
  - Updated library to use https urls ([356931e1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/356931e19ad257d63781a89fbe3229bc7103f485))
  - Updated array definition ([a169b0dd](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a169b0dd615fcc226a2625b77de1e892a5e7afe4))
  - Initial setup ([ad7465fb](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/ad7465fbb394678eeff610d417952b8b82c75a72))
  - Passed self to helper class ([5795bd1a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5795bd1a840d233eb386b64d2ce28bf5e43f25e9))
  - Set to default cron activities to true ([21439771](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/214397712e2130b88e2c43a7090e8169138313e5))
  - moved setting call out of helper class ([a08f1664](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a08f1664d7abbb6e84b2d04a866a3946b48a9f84))
  - Updated cron to use new class ([b707f308](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b707f30819dbf51a6f6f60f41dd7daa9e0521dd7))
  - Added Fitbit helper class ([a044c4bf](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/a044c4bf12aa3ae1581ad4f1ec01296b5921e71c))
  - Working cron set ([cffd50d4](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cffd50d44f34d368f6fbef22ad7ef70030311b39))
  - Added nxr function ot app ([cd985191](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/cd985191c0775c71a3f9321532aa3059559e4525))
  - Added default timezone Section comments Function to get users cooldown value ([b77e051f](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/b77e051f577dc12f04eb94868636307ee1748cda))
  - Cleaned up getting and setting values in config class ([5d5a6f10](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5d5a6f1068316ef93e08556bc2477d1bc32bcd02))
  - Prime database when settings are queried ([5eaaa564](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/5eaaa564bcff03e3aafef2a04d0261db7b7730d3))
  - Added list of supported APIs Added function to delete cron jobs from queue ([94213431](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/9421343109e153648c96011235a61700cfdebdf9))
  - Added helper function to check for supported API calls ([436145b1](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/436145b12e7610ae1c7d440abb5b092d7d93173f))
  - Stopped sql fallbacks in getCronJobs Added is user function ([0241086e](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/0241086e66c44d9a3c35420e3bd9a85881bef84f))
  - Added order to database call ([e4adaaee](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/e4adaaee614d00e763bed30e0e1db34195a156f0))
  - Initial commit of some project framework files ([61c53958](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/61c53958b763bf210c78ec5bfa4ca8a80894d977))
  - first commit ([bed47b7a](https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/commit/bed47b7a8baf29fabbb6a984d995c698c1cb0a68))




---
<sub><sup>*Generated with [git-changelog](https://nxfifteen.me.uk/gitlab/nxfifteen/git-changelog). If you have any problems or suggestions, create an issue.* :) **Thanks** </sub></sup>


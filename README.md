EC-CUBEアフィリナビトラッキングプラグイン
=========================================
自社運営型アフィリエイトシステムのアフィリナビが提供する
売上トラッキングタグの設置と設定を自動化するプラグインです。


対応製品
--------
現在以下の製品・機能に対応しています。

* AN7
    * 売上トラッキング
* アフィリナビPRO
    * 売上トラッキング


インストール方法
----------------
1. [プロジェクトをダウンロード](https://github.com/zenith6/eccube-antracking/archive/master.tar.gz "eccube-antracking-master.tar.gz")します。

    ```bash
    # プロジェクトをダウンロードする
    wget https://github.com/zenith6/eccube-antracking/archive/master.tar.gz
    ```
2. ダウンロードした書庫をインストール可能なディレクトリレイアウトに修正します。
    eccube-antracking-master 以下のファイルが、書庫のルートディレクトリへ配置されるようにして下さい。

    ```bash
    # 書庫を展開する
    tar xzvf master.tar.gz
    # 展開先の eccube-antracking-master に移動する
    cd eccube-antracking-master
    # eccube-antracking-master 以下のファイルを AnTracking.tar.gz として再圧縮する
    tar czvf ../AnTracking.tar.gz *
    ```

3. EC-CUBEの管理画面にログインし、オーナーズストア > プラグインの管理 と進みます。
4. プラグイン登録にて、2. で作成した書庫を指定しインストールします。
5. アフィリナビトラッキングプラグインのプラグイン設定を開き、各種項目を入力します。
6. プラグインを有効にします。


プラグイン設定
--------------
ご利用の製品毎に設定項目が変わります。
次の手順に従って入力して下さい。

### AN7をご利用の場合
1. AN7にログインしキャンペーンのトラッキングタグ画面を開き、売上トラッキングタグを取得します。
   ユーザー定義パラメーター「注文番号」にEC-CUBEの受注管理で使用する注文番号が記録されます。
   ※「注文番号」を定義していない場合でも記録されます。
2. EC-CUBEの管理画面にログインし、プラグインの管理を開きます。
3. アフィリナビトラッキングプラグインのプラグイン設定を開きます。
4. 『ご利用の製品』を「アフィリナビPRO」にします。
5. 1. で取得した売上トラッキングタグを「売上トラッキングタグ」に入力します。
6. [登録する] をクリックして設定を保存します。
7. プラグインの管理に戻り、アフィリナビトラッキングプラグインの『有効』をチェックします。

### アフィリナビPROをご利用の場合
1. アフィリナビPROの管理画面にログインし、売上トラッキングタグを取得します。
2. EC-CUBEの管理画面にログインし、プラグインの管理を開きます。
3. アフィリナビトラッキングプラグインのプラグイン設定を開きます。
4. 『ご利用の製品』を「アフィリナビPRO」にします。
5. 1. で取得した売上トラッキングタグを「売上トラッキングタグ」に入力します。
6. [登録する] をクリックして設定を保存します。
7. プラグインの管理に戻り、アフィリナビトラッキングプラグインの『有効』をチェックします。


トラッキングについて
-------------------
ご利用の製品毎にトラッキングされる内容が変わります。

### AN7をご利用の場合

#### 売上トラッキング
トラッキングデータにバナー売上達成が記録されます。
トラッキングデータが解析された時点でバナー売上費用コミッションが発生します。
バナー売上費用コミッションのユーザー定義パラメーター「注文番号」に
EC-CUBEの受注管理で使用する注文番号が記録されます。

### アフィリナビPROをご利用の場合

#### 売上トラッキング
報酬管理の報酬確定処理に注文のトランザクションが記録されます。
トランザクション承認画面のデータ欄に
EC-CUBEの受注管理で使用する注文番号が記録されます。


ラインセス
---------
LGPLでご利用可能です。


お問い合わせ先
--------------
アフィリナビに関するお問い合わせはアフィリナビ公式サイト
[affilinavi.com](http://affilinavi.com/ "affilinavi.com") にて受け付けております。

🧠 Kullanıcı Oturum Tahmin Sistemi

Kullanıcıların geçmiş oturum verilerini analiz ederek, bir sonraki muhtemel giriş zamanını tahmin eden PHP tabanlı bir analiz sistemi.

🚀 Özellikler

- 📈 Geçmiş giriş saatlerine göre oturum tahmini
- 🧠 Ortalama ve istatistiksel analizlere dayalı tahminleme
- 🗂️ JSON tabanlı veri saklama ve önbellekleme (cache)
- ⏱️ Zaman dilimi bazlı döngü analizleri
- 💾 Composer destekli proje yapısı

📊 Kullanılan Algoritmalar

1. Aykırı Değer Filtreleme + IQR Aralık Tahmini
- Z-Score yöntemiyle aykırı değerler hariç tutulur.
- Kalan verilerle IQR (Interquartile Range) analizi yapılır.
- Elde edilen dağılıma göre tahmini bir tarih aralığı sunulur.

2. Saat Dilimi Bazlı Olası Giriş Zamanı
- Giriş saatleri, tarih bağımsız olarak sadece saat dilimlerine göre analiz edilir.
- En yüksek yoğunluklu saat dilimi, bir sonraki olası giriş zamanı olarak önerilir.

3. Gün Bazlı Olası Giriş Günü
- Tüm girişler haftanın günlerine göre gruplandırılır.
- En çok giriş yapılan gün tespit edilerek, haftalık döngü tahmini yapılır.

4. Gün + Saat Dilimi Analizi
- Girişler, haftanın günleri ve saat dilimlerine göre gruplanır.
- Oluşturulan haftalık olasılık listesi standart sapma kontrolünden geçirilir.
  - Eğer sapma çok düşük ise analiz iptal edilir.
- Analiz geçerli ise, bugünün gün ve saatine göre bir sonraki alt sınırdan(ortalama + standart sapma) yüksek en olası zaman tahmin edilir.
  - Aynı zaman aralığında birden fazla giriş varsa, saat ortalaması alınarak tahmin yapılır.

// Copyright 2021 Google LLC
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

// COMPLETE: Import ad_helper.dart
import 'package:ranking_futebol/ad_helper.dart';
import 'package:ranking_futebol/destination.dart';
import 'package:flutter/material.dart';

// COMPLETE: Import google_mobile_ads.dart
import 'package:google_mobile_ads/google_mobile_ads.dart';

class BannerInlinePage extends StatefulWidget {
  final List<Destination> entries;

  const BannerInlinePage({
    required this.entries,
    Key? key,
  }) : super(key: key);

  @override
  State createState() => _BannerInlinePageState();
}

class _BannerInlinePageState extends State<BannerInlinePage> {
  // COMPLETE: Add _kAdIndex
  static const _kAdIndex = 4;

  // COMPLETE: Add a banner ad instance
  BannerAd? _ad;

  @override
  void initState() {
    super.initState();

    // COMPLETE: Load a banner ad
    BannerAd(
      adUnitId: AdManager.bannerAdUnitId,
      size: AdSize.banner,
      request: const AdRequest(),
      listener: BannerAdListener(
        onAdLoaded: (ad) {
          setState(() {
            _ad = ad as BannerAd;
          });
        },
        onAdFailedToLoad: (ad, error) {
          // Releases an ad resource when it fails to load
          ad.dispose();
          debugPrint(
              'Ad load failed (code=${error.code} message=${error.message})');
        },
      ),
    ).load();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('AdMob Banner Inline Ad'),
      ),
      body: ListView.builder(
        // COMPLETE: Adjust itemCount based on the ad load state
        itemCount: widget.entries.length + (_ad != null ? 1 : 0),
        itemBuilder: (context, index) {
          // COMPLETE: Render a banner ad
          if (_ad != null && index == _kAdIndex) {
            return Container(
              width: _ad!.size.width.toDouble(),
              height: 72.0,
              alignment: Alignment.center,
              child: AdWidget(ad: _ad!),
            );
          } else {
            // COMPLETE: Get adjusted item index from _getDestinationItemIndex()
            final item = widget.entries[_getDestinationItemIndex(index)];

            return ListTile(
              leading: Image.asset(
                item.asset,
                width: 48,
                height: 48,
                package: 'flutter_gallery_assets',
                fit: BoxFit.cover,
              ),
              title: Text(item.name),
              subtitle: Text(item.duration),
              onTap: () {
                debugPrint('Clicked ${item.name}');
              },
            );
          }
        },
      ),
    );
  }

  @override
  void dispose() {
    // COMPLETE: Dispose a BannerAd object
    _ad?.dispose();
    super.dispose();
  }

  // COMPLETE: Add _getDestinationItemIndex()
  int _getDestinationItemIndex(int rawIndex) {
    if (rawIndex >= _kAdIndex && _ad != null) {
      return rawIndex - 1;
    }
    return rawIndex;
  }
}

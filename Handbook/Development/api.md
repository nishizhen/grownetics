# API

## Documentation

To work on the API documentation you need to `bundle install` inside the API folder.

To get that to work you may get an error about `nokogiri` package. Run this: `gem install nokogiri -v '1.6.8.1' -- --with-xml2-include=/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/libxml2 --use-system-libraries` Now run `bundle install` again.

sen
# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'livereload' do
  watch(%r{.+\.(css|js|html?|php|inc)$})
end

# Add files and commands to this file, like the example:
#   watch(%r{file/path}) { `command(s)` }
#
guard 'shell' do
 watch(%r{^/_sass/.*\.s[ac]ss}) do
    `compass compile`
  end
end

#guard 'phpunit', :cli => '--colors' do
  #watch(%r{/^tests/*.php$})
#end

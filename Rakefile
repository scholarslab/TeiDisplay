task :default => 'test:all'

namespace :test do

  desc 'Run all tests'
  task :all do
    Rake::Task['test:server'].invoke
    # Rake::Task['jasmine:ci'].invoke
  end

  desc 'Run the PHPUnit suite'
  task :server do
    sh %{cd tests && phpunit}
  end

  # desc 'Run the Jasmine server'
  # task :jasmine do
    # sh %{rake jasmine JASMINE_PORT=1337}
  # end

end
